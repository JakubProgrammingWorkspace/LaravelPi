@extends('layouts.app')

@section('title', 'Employee: ' . $employee->full_name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-circle me-2"></i>{{ $employee->full_name }}
    </h1>
    <div>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Employees
        </a>
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit Employee
            </a>
    </div>
</div>

<div class="row">
    <!-- Personal Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-person-lines-fill me-2"></i>Personal Information
                </h5>
                <table class="table table-sm mb-0">
                    <tr>
                        <th style="width: 160px;">First Name</th>
                        <td>{{ $employee->first_name }}</td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td>{{ $employee->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $employee->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $employee->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Hire Date</th>
                        <td>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Job Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-briefcase me-2"></i>Job Information
                </h5>
                <table class="table table-sm mb-0">
                    <tr>
                        <th style="width: 160px;">Position</th>
                        <td>{{ $employee->position ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>{{ $employee->department ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Salary</th>
                        <td>{{ $employee->salary ? '$' . number_format($employee->salary, 2) : '—' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($employee->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($employee->status === 'inactive')
                                <span class="badge bg-warning text-dark">Inactive</span>
                            @else
                                <span class="badge bg-danger">Terminated</span>
                            @endif
                        </td>
                    </tr>
                    @if($employee->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $employee->notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Audit Information -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="bi bi-clock-history me-2"></i>Audit Information
        </h5>
        <table class="table table-sm mb-0">
            <tr>
                <th style="width: 200px;">Created By</th>
                <td>{{ $employee->creator ? $employee->creator->name : '—' }}</td>
            </tr>
            <tr>
                <th>Last Updated By</th>
                <td>{{ $employee->updater ? $employee->updater->name : '—' }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $employee->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $employee->updated_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
