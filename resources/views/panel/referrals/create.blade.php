@extends('layouts.app')

@section('title', 'Skierowanie na badania lekarskie')

@section('content')
<div class="mb-3">
    <a href="{{ route('referrals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Anuluj
    </a>
    <h2 class="d-inline"><i class="bi bi-file-earmark-text me-2"></i>Skierowanie na badania lekarskie</h2>
</div>

<form method="POST" action="{{ route('referrals.store') }}" id="referralForm">
    @csrf

    <!-- Employee -->
    <div class="mb-3">
        <label for="employee_id" class="form-label">Pracownik</label>
        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
            <option value="">-- Wybierz pracownika --</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                {{ $emp->full_name }}{{ $emp->pesel ? ' (PESEL: ' . $emp->pesel . ')' : '' }}
            </option>
            @endforeach
        </select>
        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Examination Type -->
    <div class="mb-3">
        <label for="examination_type" class="form-label">Typ badania</label>
        <select name="examination_type" id="examination_type" class="form-select @error('examination_type') is-invalid @enderror" required>
            <option value="">-- Wybierz typ --</option>
            <option value="wstępne" {{ old('examination_type') === 'wstępne' ? 'selected' : '' }}>wstępne</option>
            <option value="okresowe" {{ old('examination_type') === 'okresowe' ? 'selected' : '' }}>okresowe</option>
            <option value="kontrolne" {{ old('examination_type') === 'kontrolne' ? 'selected' : '' }}>kontrolne</option>
        </select>
        @error('examination_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Job Position -->
    <div class="mb-3">
        <label for="job_position" class="form-label">Stanowisko pracy</label>
        <input type="text" name="job_position" id="job_position" class="form-control @error('job_position') is-invalid @enderror"
               value="{{ old('job_position') }}">
        @error('job_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Job Description -->
    <div class="mb-3">
        <label for="job_description" class="form-label">Opis warunków pracy</label>
        <textarea name="job_description" id="job_description" class="form-control @error('job_description') is-invalid @enderror"
                  rows="3">{{ old('job_description') }}</textarea>
        @error('job_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Issue Place -->
    <div class="mb-3">
        <label for="issue_place" class="form-label">Miejsce wystawienia</label>
        <input type="text" name="issue_place" id="issue_place" class="form-control @error('issue_place') is-invalid @enderror"
               value="{{ old('issue_place') }}">
        @error('issue_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Issue Date -->
    <div class="mb-3">
        <label for="issue_date" class="form-label">Data wystawienia</label>
        <input type="date" name="issue_date" id="issue_date" class="form-control @error('issue_date') is-invalid @enderror"
               value="{{ old('issue_date', today()->format('Y-m-d')) }}" required>
        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Exposure Factors -->
    <div class="mb-4">
        <label class="form-label fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Czynniki narażenia</label>
        <div class="form-text mb-2">Zaznacz czynniki narażenia zawodowego i podaj wyniki pomiarów (opcjonalnie).</div>

        @foreach($categories as $cat)
        <div class="card border mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Kategoria {{ $cat->code }}: {{ $cat->name }}</h6>
            </div>
            <div class="card-body">
                @if($cat->factors->isEmpty())
                    <p class="text-muted small">Brak czynników w tej kategorii.</p>
                @else
                    @foreach($cat->factors as $factor)
                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="exposure_factor_ids[]" value="{{ $factor->id }}"
                                   id="factor_{{ $factor->id }}"
                                   class="form-check-input"
                                   {{ in_array($factor->id, old('exposure_factor_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="factor_{{ $factor->id }}">
                                {{ $factor->name }}
                            </label>
                        </div>
                        <input type="text" name="exposure_details[]" class="form-control form-control-sm mt-1"
                               placeholder="Wielkość narażenia / wyniki pomiarów (opcjonalnie)"
                               value="{{ old('exposure_details.' . $loop->index) }}"
                               id="details_{{ $factor->id }}">
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Zapisz
    </button>
    <a href="{{ route('referrals.index') }}" class="btn btn-outline-secondary">Anuluj</a>
</form>
@endsection
