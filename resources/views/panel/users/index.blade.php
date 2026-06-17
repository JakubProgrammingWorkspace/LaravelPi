@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-gear me-2"></i>Users
    </h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Add User
    </a>
</div>

@if($users->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No users found.
        <a href="{{ route('users.create') }}">Create the first user!</a>
    </div>
@else
    <!-- Search -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control"
                   placeholder="Search by name or email..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-info text-dark me-1">{{ ucfirst($role->name) }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end" style="white-space: nowrap;">
                            <a href="{{ route('users.show', $user) }}"
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $user) }}"
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endif
@endsection
