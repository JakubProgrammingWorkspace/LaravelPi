@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Wróć do listy
    </a>
    <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edytuj
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h4>{{ $company->name }}</h4>
        <hr>
        <table class="table table-borderless mb-0">
            <tr>
                <th style="width: 200px;">NIP:</th>
                <td>{{ $company->nip ?? '—' }}</td>
            </tr>
            <tr>
                <th>Adres:</th>
                <td>{{ $company->full_address ?: '—' }}</td>
            </tr>
            <tr>
                <th>Utworzono:</th>
                <td>{{ $company->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <th>Zaktualizowano:</th>
                <td>{{ $company->updated_at->format('d.m.Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
