@extends('layouts.app')

@section('title', 'Edit: ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-pencil me-2"></i>Edit User: {{ $user->name }}</h1>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PATCH')

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">User Information</h5>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" minlength="8">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Assign Roles</h5>

                    @if($roles->isEmpty())
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>No roles available.
                            Please create roles first in the <a href="{{ route('roles.index') }}">Roles</a> section.
                        </div>
                    @else
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="roles[]" id="role_{{ $role->id }}"
                                       value="{{ $role->id }}"
                                       {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ ucfirst($role->name) }}
                                    @if($role->description)
                                        <br><small class="text-muted">{{ $role->description }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                        @error('roles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
