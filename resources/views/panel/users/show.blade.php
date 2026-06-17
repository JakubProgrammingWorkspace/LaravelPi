@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Wróć do listy
    </a>
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edytuj
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h4>{{ $user->name }}</h4>
        <hr>
        <table class="table table-borderless mb-0">
            <tr>
                <th style="width: 150px;">E-mail:</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Role:</th>
                <td>
                    @foreach($user->roles as $role)
                    <span class="badge bg-info text-dark me-1">{{ ucfirst($role->name) }}</span>
                    @endforeach
                    @if($user->roles->isEmpty())
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Utworzono:</th>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <th>Zaktualizowano:</th>
                <td>{{ $user->updated_at->format('d.m.Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
