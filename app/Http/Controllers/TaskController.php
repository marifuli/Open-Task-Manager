<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
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

        // dd($formData);

        $project->tasks()->create($request->all());

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to_do,in_progress,completed',
        ]);

        $task->update($request->all());

        return redirect()->route('projects.tasks.index', $task->project_id)->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        // dd($request->all());
        $task->task_status_id = $request->input('task_status_id');
        $task->save();

        return response()->json(['message' => 'Task status updated successfully.']);
    }
}
