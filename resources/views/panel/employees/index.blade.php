@extends('layouts.app')

@section('title', 'Pracownicy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>Pracownicy</h2>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Dodaj pracownika
    </a>
</div>

<!-- Search & Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Szukaj po imieniu, nazwisku, PESEL, e-mail..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Wszystkie statusy</option>
                    @foreach(['active' => 'Aktywni', 'inactive' => 'Nieaktywni', 'terminated' => 'Zwolnieni'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="company_id" class="form-select">
                    <option value="">Wszystkie firmy</option>
                    @foreach($companies as $comp)
                    <option value="{{ $comp->id }}" {{ request('company_id') == $comp->id ? 'selected' : '' }}>
                        {{ $comp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Szukaj
                </button>
            </div>
            @if(request('search') || request('status') || request('company_id'))
            <div class="col-md-2">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary w-100">Wyczyść</a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($employees->isEmpty())
            <p class="text-muted text-center">Brak pracowników w systemie.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Imię i Nazwisko</th>
                            <th>PESEL</th>
                            <th>Firma</th>
                            <th>Stanowisko</th>
                            <th>Dział</th>
                            <th>Status</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->pesel ?? '—' }}</td>
                            <td>{{ $employee->company ? $employee->company->name : '—' }}</td>
                            <td>{{ $employee->position ?: '—' }}</td>
                            <td>{{ $employee->department ?: '—' }}</td>
                            <td>
                                <span class="badge {{
                                    $employee->status === 'active' ? 'bg-success' :
                                    ($employee->status === 'inactive' ? 'bg-warning text-dark' : 'bg-danger')
                                }}">
                                    {{ $employee->status === 'active' ? 'Aktywny' : ($employee->status === 'inactive' ? 'Nieaktywny' : 'Zwolniony') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-info" title="Pokaż">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning" title="Edytuj">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Czy na pewno usunąć tego pracownika?');">
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
            {{ $employees->links() }}
        @endif
    </div>
</div>
@endsection
