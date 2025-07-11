<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\TaskStatus;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        $tasks = $project->tasks()->get()->groupBy('task_status_id');

        // dd($tasks);
        $users = $project->users()->get();
        $taskStatuses = TaskStatus::orderBy('order')->get();
        return view('tasks.index', compact('project', 'tasks', 'users', 'taskStatuses'));
    }

    public function store(Request $request, Project $project)
    {
        $formData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'expected_completion_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        $task = $project->tasks()->create($request->all());

        // Create task history
        $taskHistory = TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'created',
        ]);

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $statuses = TaskStatus::orderBy('order')->get();
        return view('tasks.show', compact('task', 'statuses'));
    }

    public function update(Request $request, Task $task)
    {
        $formData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        // First assign new values manually (don't save yet)
        $task->fill($formData);

        // Now get the changed fields
        $changedFields = [];

        foreach ($task->getDirty() as $field => $newValue) {
            $changedFields[$field] = [
                'old' => $task->getOriginal($field),
                'new' => $newValue,
            ];
        }
        // dd($changedFields);
        // Now perform the update
        $task->save();

        // Save task history only if something changed
        if (!empty($changedFields)) {
            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'updated',
                'changed_field' => $changedFields,
            ]);
        }

        return redirect()
            ->route('projects.tasks.index', $task->project_id)
            ->with('success', 'Task updated successfully.');
    }


    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        $oldStatus = $task->task_status_id;
        $newStatus = $validated['task_status_id'];

        if ($oldStatus != $newStatus) {
            $task->task_status_id = $newStatus;
            $task->save();

            TaskHistory::create([
                'task_id' => $task->id,
                'changed_field' => [
                    'task_status_id' => [
                        'old' => $oldStatus,
                        'new' => $newStatus,
                    ],
                ],
                'action' => 'status',
            ]);
        }

        // dd($task->task_status_id);
        if ($task->task_status_id == 11) {
            $currentDate = now();

            $expectedCompletionDate = $task->expected_completion_date
                ? Carbon::parse($task->expected_completion_date)
                : null;

            if ($expectedCompletionDate) {
                $diffInHours = $expectedCompletionDate->diffInHours($currentDate, false); // false = allow negative

                // Set default points and reason
                $points = 0;
                $reason = 'Task completed exactly on expected date';

                if ($diffInHours < 0) {
                    // Completed early → reward
                    $points = abs($diffInHours);
                    $reason = 'Task completed before expected date';
                } elseif ($diffInHours > 0) {
                    // Completed late → penalty
                    $points = -$diffInHours;
                    $reason = 'Task completed after expected date';
                }

                // Always create or get the monthly point record
                $userPoint = UserPoint::firstOrCreate(
                    [
                        'user_id' => $task->user_id,
                        'month' => now()->format('Y-m-01'),
                    ],
                    [
                        'points' => 0,
                    ]
                );

                // Apply point change only if non-zero
                if ($points > 0) {
                    $userPoint->increment('points', $points);
                } elseif ($points < 0) {
                    $userPoint->decrement('points', abs($points));
                }

                // Log to point history regardless of point amount (optional: skip if 0)
                $userPoint->histories()->create([
                    'task_id' => $task->id,
                    'points' => $points,
                    'reason' => $reason,
                ]);
            }
        }


        return response()->json(['message' => 'Task status updated successfully.']);
    }


    // user points update section
    public function adjustPoints(Request $request, Task $task)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
            'adjust_type' => 'required|in:increment,decrement',
        ]);

        $points = $request->input('points');
        $reason = $request->input('reason');
        $adjustType = $request->input('adjust_type');
        $userPoint = UserPoint::firstOrCreate(
            [
                'user_id' => $task->user_id,
                'month' => now()->format('Y-m-01'),
            ],
            [
                'points' => 0,
            ]
        );
        // Adjust points based on type
        if ($adjustType === 'increment') {
            $userPoint->increment('points', $points);
        } elseif ($adjustType === 'decrement') {
            $userPoint->decrement('points', $points);
        }
        // Log the point history
        $userPoint->histories()->create([
            'task_id' => $task->id,
            'points' => $points,
            'reason' => $reason,
        ]); 
        
        return redirect()->route('tasks.show', $task->id)
            ->with('success', 'Points adjusted successfully.');

    }
}
