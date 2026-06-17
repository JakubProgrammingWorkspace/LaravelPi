@extends('layouts.app')

@section('title', 'Skierowania')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Skierowania</h2>
    <a href="{{ route('referrals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nowe skierowanie
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($referrals->isEmpty())
            <p class="text-muted text-center">Brak skierowań w systemie.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pracownik</th>
                            <th>Typ badania</th>
                            <th>Stanowisko</th>
                            <th>Data wystawienia</th>
                            <th>Status PDF</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr>
                            <td>{{ $referral->employee->full_name }}</td>
                            <td>
                                <span class="badge {{
                                    $referral->examination_type === 'wstępne' ? 'bg-info' :
                                    ($referral->examination_type === 'okresowe' ? 'bg-warning text-dark' : 'bg-success')
                                }}">
                                    {{ $referral->type_label }}
                                </span>
                            </td>
                            <td>{{ $referral->job_position ?: '—' }}</td>
                            <td>{{ $referral->issue_date->format('d.m.Y') }}</td>
                            <td>
                                @if($referral->hasPdf())
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Wygenerowano</span>
                                @else
                                    <span class="badge bg-secondary">Nie wygenerowano</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('referrals.show', $referral) }}" class="btn btn-sm btn-outline-info" title="Pokaż">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($referral->hasPdf())
                                {{-- PDF already generated: direct download link --}}
                                <a href="{{ route('referrals.pdf', $referral) }}" class="btn btn-sm btn-primary" title="Pobierz PDF">
                                    <i class="bi bi-download me-1"></i>Pobierz PDF
                                </a>
                                @else
                                {{-- No PDF yet: generate it first --}}
                                <form action="{{ route('referrals.generate-pdf', $referral) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Generuj PDF
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('referrals.destroy', $referral) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Czy na pewno usunąć to skierowanie?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $referrals->links() }}
        @endif
    </div>
</div>
@endsection
