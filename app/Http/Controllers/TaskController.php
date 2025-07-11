<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return response()->json(['message' => 'Task status updated successfully.']);
    }
}
