@extends('layouts.app')
@section('title')
    Projects 
@endsection

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Projects</h2>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Project
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            @forelse($projects as $project)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary fw-bold">{{ $project->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>

                            <div class="mb-2">
                                <strong>Status:</strong>
                                <span class="badge 
                                    {{ $project->status === 'pending' ? 'bg-secondary' : ($project->status === 'on_going' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <strong>Deadline:</strong>
                                @if($project->end_date && $project->end_date->isFuture())
                                    <span class="text-success">{{ $project->end_date->diffForHumans() }}</span>
                                @else
                                    <span class="text-danger">Deadline Passed</span>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('projects.tasks.index', $project->id) }}" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-kanban-fill me-1"></i> Manage Tasks
                                    </a>
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-info w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-outline-warning w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center" onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No projects found. <a href="{{ route('projects.create') }}">Create one now</a>.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
