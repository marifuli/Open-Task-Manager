@extends('layouts.app')
@section('title', 'User Points')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>User Points</h2>
        </div>

        {{-- Filters --}}
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="user_id" class="form-select">
                    <option value="">Filter by User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <select name="month" class="form-select">
                    <option value="">Filter by Month</option>
                    @foreach ($months as $month)
                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <button class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>

        {{-- Table --}}
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Points</th>
                    <th>Month</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($userPoints as $userPoint)
                    <tr>
                        <td>{{ $userPoint->user->name ?? 'N/A' }}</td>
                        <td>{{ $userPoint->points }}</td>
                        <td>{{ \Carbon\Carbon::parse($userPoint->month)->format('F Y') }}</td>
                        <td>
                            <a href="{{ route('points.histories.show', $userPoint->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Show
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No user points found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
