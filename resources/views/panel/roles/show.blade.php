@extends('layouts.app')

@section('title', 'Role: ' . ucfirst($role->name))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-shield-lock me-2"></i>{{ ucfirst($role->name) }}
    </h1>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Roles
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Role Details</h5>
        <table class="table table-sm">
            <tr>
                <th style="width: 200px;">Name</th>
                <td>{{ ucfirst($role->name) }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $role->description ?? '—' }}</td>
            </tr>
            <tr>
                <th>Users with this role</th>
                <td>{{ $role->users->count() }}</td>
            </tr>
            <tr>
                <th>Created</th>
                <td>{{ $role->created_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>
</div>

@if($role->users->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No users have been assigned this role.
    </div>
@else
    <h3 class="h5 mb-3">
        <i class="bi bi-people me-2"></i>Assigned Users ({{ $role->users->count() }})
    </h3>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Other Roles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($role->users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $uRole)
                                @if($uRole->id !== $role->id)
                                    <span class="badge bg-info text-dark me-1">{{ ucfirst($uRole->name) }}</span>
                                @endif
                            @endforeach
                            @if($user->roles->count() <= 1)
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
