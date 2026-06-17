@extends('layouts.app')

@section('title', $role->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Wróć do listy
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h4>{{ $role->name }}</h4>
        <p class="text-muted">{{ $role->description }}</p>
        <hr>
        <h5>Użytkownicy z tą rolą ({{ $role->users->count() }})</h5>
        @if($role->users->isEmpty())
            <p class="text-muted">Brak użytkowników z tą rolą.</p>
        @else
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Imię</th>
                        <th>E-mail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($role->users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
