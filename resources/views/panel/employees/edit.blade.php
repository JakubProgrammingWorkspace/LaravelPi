@extends('layouts.app')

@section('title', 'Edit: ' . $employee->full_name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-pencil me-2"></i>Edit: {{ $employee->full_name }}</h1>
        </div>

        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf
            @method('PATCH')

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Personal Information</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $employee->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $employee->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $employee->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $employee->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hire_date" class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date"
                                   class="form-control @error('hire_date') is-invalid @enderror"
                                   value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
                            @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Job Information</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" id="position"
                                   class="form-control @error('position') is-invalid @enderror"
                                   value="{{ old('position', $employee->position) }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department"
                                   class="form-control @error('department') is-invalid @enderror"
                                   value="{{ old('department', $employee->department) }}">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" name="salary" id="salary"
                                   class="form-control @error('salary') is-invalid @enderror"
                                   value="{{ old('salary', $employee->salary) }}">
                            @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $employee->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
