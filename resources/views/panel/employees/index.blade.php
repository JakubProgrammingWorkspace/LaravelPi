@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people me-2"></i>Employees
    </h1>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Add Employee
    </a>
</div>

@if($employees->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No employees found.
        <a href="{{ route('employees.create') }}">Add the first employee!</a>
    </div>
@else
    <!-- Search & Filters -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   placeholder="Search by name, email, position, or department..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="bi bi-search me-1"></i>Filter
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
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Hire Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </a>
                        </td>
                        <td>{{ $employee->email ?? '—' }}</td>
                        <td>{{ $employee->position ?? '—' }}</td>
                        <td>{{ $employee->department ?? '—' }}</td>
                        <td>
                            @if($employee->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($employee->status === 'inactive')
                                <span class="badge bg-warning text-dark">Inactive</span>
                            @else
                                <span class="badge bg-danger">Terminated</span>
                            @endif
                        </td>
                        <td>{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—' }}</td>
                        <td class="text-end" style="white-space: nowrap;">
                            <a href="{{ route('employees.show', $employee) }}"
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}"
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('employees.destroy', $employee) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to remove this employee?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
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
        {{ $employees->links() }}
    </div>
@endif
@endsection
