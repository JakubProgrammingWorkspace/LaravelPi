@extends('layouts.app')

@section('title', 'Czynniki narażeń')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-exclamation-triangle me-2"></i>Czynniki narażeń</h2>
    <a href="{{ route('exposure-factors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Dodaj czynnik
    </a>
</div>

@if(isset($categories) && $categories->count() > 0)
    @foreach($categories as $cat)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Kategoria {{ $cat->code }}: {{ $cat->name }}</h5>
        </div>
        <div class="card-body">
            @if($cat->factors->isEmpty())
                <p class="text-muted">Brak czynników w tej kategorii.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nazwa czynnika</th>
                                <th>Opis</th>
                                <th class="text-end">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cat->factors as $factor)
                            <tr>
                                <td>{{ $factor->name }}</td>
                                <td>{{ $factor->description ?: '—' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('exposure-factors.destroy', $factor) }}" method="POST"
                                        onsubmit="return confirm('Czy na pewno usunąć ten czynnik?');">
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
            @endif
        </div>
    </div>
    @endforeach
@elseif(isset($factors))
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            @if($factors->isEmpty())
                <p class="text-muted">Brak czynników narażenia.</p>
            @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kategoria</th>
                            <th>Nazwa</th>
                            <th>Opis</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factors as $factor)
                        <tr>
                            <td>{{ $factor->category ? $factor->category->name : '—' }}</td>
                            <td>{{ $factor->name }}</td>
                            <td>{{ $factor->description ?: '—' }}</td>
                            <td class="text-end">
                                <form action="{{ route('exposure-factors.destroy', $factor) }}" method="POST"
                                    onsubmit="return confirm('Czy na pewno usunąć ten czynnik?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endif
@endsection
