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

    // Only continue if the status has changed
    if ($oldStatus != $newStatus) {
        $task->task_status_id = $newStatus;
        $task->save();

        // Log status change
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

        // 👉 If transitioning INTO "Completed" (ID 9) from a lower status → evaluate expected date
        if ($newStatus == 9 && $oldStatus < 9) {
            $currentDate = now();
            $expectedCompletionDate = $task->expected_completion_date
                ? Carbon::parse($task->expected_completion_date)
                : null;

            if ($expectedCompletionDate) {
                $currentDateOnly = $currentDate->copy()->startOfDay();
                $expectedDateOnly = $expectedCompletionDate->copy()->startOfDay();

                $points = 0;
                $reason = 'Task completed exactly on expected date';

                if ($currentDateOnly->lt($expectedDateOnly)) {
                    $diffInHours = $expectedCompletionDate->diffInHours($currentDate, false);
                    $points = abs($diffInHours);
                    $reason = 'Task completed before expected date';
                } elseif ($currentDateOnly->gt($expectedDateOnly)) {
                    $diffInHours = $expectedCompletionDate->diffInHours($currentDate, false);
                    $points = -$diffInHours;
                    $reason = 'Task completed after expected date';
                }

                $userPoint = UserPoint::firstOrCreate(
                    [
                        'user_id' => $task->user_id,
                        'month' => now()->format('Y-m-01'),
                    ],
                    ['points' => 0]
                );

                if ($points > 0) {
                    $userPoint->increment('points', $points);
                } elseif ($points < 0) {
                    $userPoint->decrement('points', abs($points));
                }

                $userPoint->histories()->create([
                    'task_id' => $task->id,
                    'points' => $points,
                    'reason' => $reason,
                ]);
            }
        }

        // 👉 If transitioning OUT OF completed/review/test statuses (IDs 9,10,11,12) → deduct 10 points
        if (in_array($oldStatus, [9, 10, 11, 12]) && in_array($newStatus, [1, 2, 3, 4, 5, 6, 7, 8])) {
            $penaltyPoints = 10;
            $penaltyReason = 'Task regressed after completion/review';

            $userPoint = UserPoint::firstOrCreate(
                [
                    'user_id' => $task->user_id,
                    'month' => now()->format('Y-m-01'),
                ],
                ['points' => 0]
            );

            $userPoint->decrement('points', $penaltyPoints);

            $userPoint->histories()->create([
                'task_id' => $task->id,
                'points' => -$penaltyPoints,
                'reason' => $penaltyReason,
            ]);
        }
    }

    return response()->json(['message' => 'Task status updated successfully.']);
}




    // user points update section
    public function adjustPoints(Request $request, Task $task)
    {
        $formData = $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
            'adjust_type' => 'required|in:increment,decrement',
        ]);

        $userPoint = UserPoint::firstOrCreate(
            [
                'user_id' => $task->user_id,
                'month' => now()->format('Y-m-01'),
            ],
            [
                'points' => 0,
            ]
        );

        // Adjust points based on the type
        if ($formData['adjust_type'] === 'increment') {
            $userPoint->increment('points', $formData['points']);
        } else {
            $userPoint->decrement('points', $formData['points']);
        }

        // Log the point adjustment
        $userPoint->histories()->create([
            'task_id' => $task->id,
            'points' => $formData['points'],
            'reason' => $formData['reason'],
        ]);

        return redirect()->route('tasks.show', $task->id)
            ->with('success', 'Points adjusted successfully.');
    }
}
