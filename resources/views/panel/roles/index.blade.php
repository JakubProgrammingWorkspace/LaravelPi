@extends('layouts.app')

@section('title', 'Role')

@section('content')
<div class="mb-4">
    <h2><i class="bi bi-shield-check me-2"></i>Role</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($roles->isEmpty())
            <p class="text-muted text-center">Brak ról w systemie.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nazwa roli</th>
                            <th>Opis</th>
                            <th>Użytkownicy</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td><a href="{{ route('roles.show', $role) }}" class="text-decoration-none">{{ $role->name }}</a></td>
                            <td>{{ $role->description ?: '—' }}</td>
                            <td>{{ $role->users_count }} użytkownik(ów)</td>
                            <td class="text-end">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="Pokaż">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!$role->users_count)
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Czy na pewno usunąć tę rolę?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $roles->links() }}
        @endif
    </div>
</div>
@endsection
