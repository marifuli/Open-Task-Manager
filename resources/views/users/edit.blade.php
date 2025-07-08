@extends('layouts.app')
@section('title')
    {{ $project->name }} Edit Project
@endsection
@section('content')
    <div class="container">
        <h2 class="mb-4 shadow-sm p-3 rounded bg-white">Edit Project</h2>
        <div class="card border-0 shadow-sm m-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}"
                            required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}"
                            required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="is_admin" class="form-label">Is Admin</label>
                        <select name="is_admin" id="is_admin" class="form-select" required>
                            <option value="0" {{ $user->is_admin == false ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $user->is_admin == true ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('is_admin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>

            </div>
        </div>
    </div>
@endsection
