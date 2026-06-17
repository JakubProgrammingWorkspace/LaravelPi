@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
<div class="mb-3">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Wróć do listy
    </a>
    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edytuj
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h4>{{ $employee->full_name }}</h4>
        <hr>
        <table class="table table-borderless mb-0">
            <tr>
                <th style="width: 200px;">PESEL:</th>
                <td>{{ $employee->pesel ?? '—' }}</td>
            </tr>
            <tr>
                <th>E-mail:</th>
                <td>{{ $employee->email ?? '—' }}</td>
            </tr>
            <tr>
                <th>Telefon:</th>
                <td>{{ $employee->phone ?? '—' }}</td>
            </tr>
            <tr>
                <th>Adres:</th>
                <td>{{ $employee->address ?? '—' }}</td>
            </tr>
            <tr>
                <th>Firma:</th>
                <td>{{ $employee->company ? $employee->company->name : '—' }}</td>
            </tr>
            <tr>
                <th>Stanowisko:</th>
                <td>{{ $employee->position ?: '—' }}</td>
            </tr>
            <tr>
                <th>Dział:</th>
                <td>{{ $employee->department ?: '—' }}</td>
            </tr>
            <tr>
                <th>Data zatrudnienia:</th>
                <td>{{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : '—' }}</td>
            </tr>
            <tr>
                <th>Pensja:</th>
                <td>{{ $employee->salary ? number_format($employee->salary, 2, ',', ' ') . ' zł' : '—' }}</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>
                    <span class="badge {{
                        $employee->status === 'active' ? 'bg-success' :
                        ($employee->status === 'inactive' ? 'bg-warning text-dark' : 'bg-danger')
                    }}">
                        {{ $employee->status === 'active' ? 'Aktywny' : ($employee->status === 'inactive' ? 'Nieaktywny' : 'Zwolniony') }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Uwagi:</th>
                <td>{{ $employee->notes ?: '—' }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
