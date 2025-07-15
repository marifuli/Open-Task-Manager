@extends('layouts.app')
@section('title', 'Point History Detail')

@section('content')
    <div class="container">
        <div class="mb-4">
            <h3>Point Summary</h3>
            <div class="card">
                <div class="card-body">
                    <p><strong>User:</strong> {{ $userPoint->user->name ?? 'N/A' }}</p>
                    <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($userPoint->month)->format('F Y') }}</p>
                    <p><strong>Total Points:</strong> {{ $userPoint->points }}</p>
                </div>
            </div>
        </div>

        <div>
            <h4>Task History</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Points</th>
                        <th>Reason</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userPoint->histories as $history)
                        <tr>
                            <td>{{ $history->task->name ?? 'Task #' . $history->task_id }}</td>
                            <td>{{ $history->points }}</td>
                            <td>{{ $history->reason }}</td>
                            <td>{{ $history->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No task history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
