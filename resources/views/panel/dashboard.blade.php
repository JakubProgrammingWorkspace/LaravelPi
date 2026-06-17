@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-1">Ilość firm</h5>
                    <h2 class="mb-0 text-primary">{{ $companies }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-1">Ilość pracowników</h5>
                    <h2 class="mb-0 text-success">{{ $employees }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text text-warning" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-1">Ilość skierowań</h5>
                    <h2 class="mb-0 text-warning">{{ $referrals }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-check text-info" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-1">Wygenerowane PDF</h5>
                    <h2 class="mb-0 text-info">{{ $referralsWithPdf }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
