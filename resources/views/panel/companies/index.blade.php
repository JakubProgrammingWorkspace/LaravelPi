@extends('layouts.app')

@section('title', 'Firmy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building me-2"></i>Firmy</h2>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Dodaj firmę
    </a>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('companies.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Szukaj po nazwie lub NIP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Szukaj
                </button>
            </div>
            @if(request('search'))
            <div class="col-md-2">
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary w-100">Wyczyść</a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Companies Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($companies->isEmpty())
            <p class="text-muted text-center">Brak firm w systemie.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nazwa</th>
                            <th>NIP</th>
                            <th>Adres</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->nip }}</td>
                            <td>{{ $company->full_address }}</td>
                            <td class="text-end">
                                <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-info" title="Pokaż">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-warning" title="Edytuj">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Czy na pewno usunąć tę firmę?');">
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
            {{ $companies->links() }}
        @endif
    </div>
</div>
@endsection
