@extends('layouts.app')

@section('title', 'Edytuj pracownika')

@section('content')
<div class="mb-3">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Anuluj
    </a>
    <h2 class="d-inline"><i class="bi bi-pencil-square me-2"></i>Edytuj pracownika</h2>
</div>

<form method="POST" action="{{ route('employees.update', $employee) }}">
    @csrf
    @method('PATCH')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">Imię</label>
            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $employee->first_name) }}" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Nazwisko</label>
            <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $employee->last_name) }}" required>
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="pesel" class="form-label">PESEL (11 cyfr)</label>
        <input type="text" name="pesel" id="pesel" class="form-control @error('pesel') is-invalid @enderror"
               value="{{ old('pesel', $employee->pesel) }}" maxlength="11" pattern="[0-9]{11}">
        <div class="form-text">PESEL musi składać się z dokładnie 11 cyfr.</div>
        @error('pesel')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $employee->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $employee->phone) }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Adres</label>
        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $employee->address) }}" placeholder="ul. miasto kod pocztowy">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="company_id" class="form-label">Firma</label>
        <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror">
            <option value="">-- Brak --</option>
            @foreach($companies as $comp)
            <option value="{{ $comp->id }}" {{ old('company_id', $employee->company_id) == $comp->id ? 'selected' : '' }}>
                {{ $comp->name }}
            </option>
            @endforeach
        </select>
        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="position" class="form-label">Stanowisko</label>
            <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror"
                   value="{{ old('position', $employee->position) }}">
            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="department" class="form-label">Dział</label>
            <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror"
                   value="{{ old('department', $employee->department) }}">
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="hire_date" class="form-label">Data zatrudnienia</label>
            <input type="date" name="hire_date" id="hire_date" class="form-control @error('hire_date') is-invalid @enderror"
                   value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
            @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="salary" class="form-label">Pensja</label>
            <input type="number" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror"
                   value="{{ old('salary', $employee->salary) }}" step="0.01" min="0">
            @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktywny</option>
            <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Nieaktywny</option>
            <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Zwolniony</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Uwagi</label>
        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                  rows="3">{{ old('notes', $employee->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Zapisz
    </button>
    <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Anuluj</a>
</form>
@endsection
