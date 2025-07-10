@extends('layouts.app')

@section('title')
    {{ $user->name }} - Profile
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">

                            {{-- Profile Image --}}
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <img src="{{ $user->profile_photo_url ?? asset('default-profile.png') }}"
                                     alt="{{ $user->name }}"
                                     class="rounded-circle img-thumbnail"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>

                            {{-- Profile Details --}}
                            <div class="col-md-9">
                                <h3 class="mb-1">{{ $user->name }}</h3>
                                <p class="text-muted mb-2">{{ $user->email }}</p>

                                <p><strong>Role:</strong> {{ ucfirst($user->role ?? 'N/A') }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    @if ($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </p>
                                <p><strong>Joined:</strong> {{ $user->created_at->format('F d, Y') }}</p>

                                @if ($user->description)
                                    <hr>
                                    <p><strong>About:</strong><br>{{ $user->description }}</p>
                                @endif

                                {{-- Edit Profile Button --}}
                                @if (auth()->user()->id === $user->id || auth()->user()->role === 'admin')
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary mt-3">
                                        <i class="bi bi-pencil-square"></i> Edit Profile
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Optional: User's Projects --}}
                @if (isset($user->projects) && $user->projects->count())
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h5>{{ $user->name }}'s Projects</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($user->projects as $project)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $project->name }}
                                        <span class="badge bg-primary">{{ ucfirst($project->status) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
