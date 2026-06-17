@extends('layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-circle me-2"></i>{{ $user->name }}
    </h1>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit User
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">User Details</h5>
        <table class="table table-sm">
            <tr>
                <th style="width: 200px;">Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Roles</th>
                <td>
                    @foreach($user->roles as $role)
                        <span class="badge bg-info text-dark me-1">{{ ucfirst($role->name) }}</span>
                    @endforeach
                    @if($user->roles->isEmpty())
                        <span class="text-muted">— No roles assigned</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Registered</th>
                <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
