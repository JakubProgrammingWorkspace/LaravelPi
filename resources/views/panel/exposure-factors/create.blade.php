@extends('layouts.app')

@section('title', 'Dodaj czynnik narażenia')

@section('content')
<div class="mb-3">
    <a href="{{ route('exposure-factors.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Anuluj
    </a>
    <h2 class="d-inline"><i class="bi bi-plus-circle me-2"></i>Dodaj czynnik narażenia</h2>
</div>

<form method="POST" action="{{ route('exposure-factors.store') }}">
    @csrf

    <div class="mb-3">
        <label for="exposure_category_id" class="form-label">Kategoria</label>
        <select name="exposure_category_id" id="exposure_category_id" class="form-select @error('exposure_category_id') is-invalid @enderror" required>
            <option value="">-- Wybierz kategorię --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('exposure_category_id') == $cat->id ? 'selected' : '' }}>
                Kategoria {{ $cat->code }}: {{ $cat->name }}
            </option>
            @endforeach
        </select>
        @error('exposure_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Nazwa czynnika</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Opis</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                  rows="3">{{ old('description') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Zapisz
    </button>
    <a href="{{ route('exposure-factors.index') }}" class="btn btn-outline-secondary">Anuluj</a>
</form>
@endsection
