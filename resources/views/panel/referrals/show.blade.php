@extends('layouts.app')

@section('title', 'Skierowanie #' . $referral->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('referrals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Wróć do listy
    </a>
    @if(!$referral->hasPdf())
    <form action="{{ route('referrals.generate-pdf', $referral) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-file-earmark-pdf me-1"></i>Generuj PDF
        </button>
    </form>
    @endif
    @if($referral->hasPdf())
    <a href="{{ route('referrals.pdf', $referral) }}" class="btn btn-primary">
        <i class="bi bi-download me-1"></i>Pobierz PDF
    </a>
    @endif
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h4>Skierowanie #{{ $referral->id }}</h4>
        <hr>
        <table class="table table-borderless mb-0">
            <tr>
                <th style="width: 220px;">Pracownik:</th>
                <td>{{ $referral->employee->full_name }}</td>
            </tr>
            <tr>
                <th>PESEL:</th>
                <td>{{ $referral->employee->pesel ?? '—' }}</td>
            </tr>
            <tr>
                <th>Firma:</th>
                <td>{{ $referral->employee->company ? $referral->employee->company->name : '—' }}</td>
            </tr>
            <tr>
                <th>Typ badania:</th>
                <td>
                    <span class="badge {{
                        $referral->examination_type === 'wstępne' ? 'bg-info' :
                        ($referral->examination_type === 'okresowe' ? 'bg-warning text-dark' : 'bg-success')
                    }}">
                        {{ $referral->type_label }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Stanowisko pracy:</th>
                <td>{{ $referral->job_position ?: '—' }}</td>
            </tr>
            <tr>
                <th>Opis warunków pracy:</th>
                <td>{{ $referral->job_description ?: '—' }}</td>
            </tr>
            <tr>
                <th>Miejsce wystawienia:</th>
                <td>{{ $referral->issue_place ?: '—' }}</td>
            </tr>
            <tr>
                <th>Data wystawienia:</th>
                <td>{{ $referral->issue_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <th>Wygenerował:</th>
                <td>{{ $referral->creator ? $referral->creator->name : '—' }}</td>
            </tr>
            <tr>
                <th>Status PDF:</th>
                <td>
                    @if($referral->hasPdf())
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Wygenerowano {{ $referral->pdf_generated_at->format('d.m.Y H:i') }}</span>
                    @else
                        <span class="badge bg-secondary">Nie wygenerowano</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Exposure Factors Details -->
@if($referral->exposureFactors->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5><i class="bi bi-exclamation-triangle me-2"></i>Czynniki narażenia ({{ $referral->exposureFactors->count() }} łącznie)</h5>
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Kategoria</th>
                    <th>Nazwa czynnika</th>
                    <th>Wielkość narażenia / wyniki pomiarów</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referral->exposureFactors as $refFactor)
                <tr>
                    <td>{{ $refFactor->exposureFactor->category ? $refFactor->exposureFactor->category->name : '—' }}</td>
                    <td>{{ $refFactor->exposureFactor->name }}</td>
                    <td>{{ $refFactor->exposure_details ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="mt-3">
    <form action="{{ route('referrals.destroy', $referral) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Czy na pewno usunąć to skpierowanie?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash me-1"></i>Usuń skierowanie
        </button>
    </form>
</div>
@endsection
