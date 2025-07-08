@extends('layouts.app')
@section('title')
    Projects 
@endsection
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Users</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">Add Users</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            @foreach($users as $user)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="card-text">{{ $user->email }}</p>
                            <p class="card-text">
                                <strong>Status:</strong> {{ $user->status == 'pending' ? 'Pending' : ($user->status == 'on_going' ? 'In Progress' : 'Completed') }}<br>
                                <strong>Deadline:</strong> 
                                @if($user->end_date && $user->end_date->isFuture())
                                    {{ $user->end_date->diffForHumans() }}
                                @else
                                    <span class="text-danger">Deadline Passed</span>
                                @endif
                            </p>
                            <a href="{{ route('projects.tasks.index', $user->id) }}" class="btn btn-primary"> <i class="bi bi-list"></i> </a>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-primary"> <i class="bi bi-eye"></i> </a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning"> <i class="bi bi-pencil-square"></i> </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?')"> <i class="bi bi-trash"></i> </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
