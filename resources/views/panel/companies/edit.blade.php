@extends('layouts.app')

@section('title', 'Edytuj firmę')

@section('content')
<div class="mb-3">
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Anuluj
    </a>
    <h2 class="d-inline"><i class="bi bi-pencil-square me-2"></i>Edytuj firmę</h2>
</div>

<form method="POST" action="{{ route('companies.update', $company) }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="name" class="form-label">Nazwa firmy</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $company->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="nip" class="form-label">NIP (10 cyfr)</label>
        <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror"
               value="{{ old('nip', $company->nip) }}" maxlength="10" pattern="[0-9]{10}">
        <div class="form-text">NIP musi mieć dokładnie 10 cyfr.</div>
        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="street" class="form-label">Ulica</label>
        <input type="text" name="street" id="street" class="form-control @error('street') is-invalid @enderror"
               value="{{ old('street', $company->street) }}">
        @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">Miasto</label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror"
                   value="{{ old('city', $company->city) }}">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="postal_code" class="form-label">Kod pocztowy</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                   value="{{ old('postal_code', $company->postal_code) }}">
            @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Zapisz
    </button>
    <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary">Anuluj</a>
</form>
@endsection
