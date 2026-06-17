@extends('layouts.app')

@section('title', 'Edytuj użytkownika')

@section('content')
<div class="mb-3">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Anuluj
    </a>
    <h2 class="d-inline"><i class="bi bi-pencil-square me-2"></i>Edytuj użytkownika</h2>
</div>

<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="name" class="form-label">Nazwa</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Nowe hasło (opcjonalnie)</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
        <div class="form-text">Wpisz tylko jeśli chcesz zmienić hasło. Minimum 8 znaków.</div>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Potwierdź hasło</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Role</label>
        @foreach($roles as $role)
        <div class="form-check">
            <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                   class="form-check-input"
                   {{ $user->roles->contains($role) || in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
            <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
        </div>
        @endforeach
        @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Zapisz
    </button>
    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">Anuluj</a>
</form>
@endsection
