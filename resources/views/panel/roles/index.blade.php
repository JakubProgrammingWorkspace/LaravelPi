@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-shield-check me-2"></i>Roles
    </h1>
</div>

@if($roles->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No roles found.
    </div>
@else
    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>
                            <a href="{{ route('roles.show', $role) }}" class="text-decoration-none">
                                <i class="bi bi-shield-lock me-1"></i>
                                {{ ucfirst($role->name) }}
                            </a>
                        </td>
                        <td>{{ $role->description ?? '—' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $role->users_count }} user(s)</span>
                        </td>
                        <td class="text-end" style="white-space: nowrap;">
                            <a href="{{ route('roles.show', $role) }}"
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('roles.destroy', $role) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                        {{ $role->users_count > 0 ? 'disabled' : '' }}>
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
        {{ $roles->links() }}
    </div>
@endif
@endsection
