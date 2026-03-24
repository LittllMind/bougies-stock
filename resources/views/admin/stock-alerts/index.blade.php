@extends('layouts.admin')

@section('title', 'Alertes de Stock')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            Alertes de Stock
        </h1>
        <span class="badge bg-danger fs-6">
            {{ $alertesEnAttente }} en attente
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="alertesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="en-attente-tab" data-bs-toggle="tab" 
                            data-bs-target="#en-attente" type="button" role="tab">
                        🚨 En attente ({{ $alertesEnAttente }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="resolues-tab" data-bs-toggle="tab" 
                            data-bs-target="#resolues" type="button" role="tab">
                        ✅ Résolues ({{ $alertesResolues }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="toutes-tab" data-bs-toggle="tab" 
                            data-bs-target="#toutes" type="button" role="tab">
                        📋 Toutes ({{ $alertes->total() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="alertesTabsContent">
                
                <div class="tab-pane fade show active" id="en-attente" role="tabpanel">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Produit</th>
                                <th>Stock</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertes->filter(fn($a) => !$a->resolue) as $alerte)
                                @include('admin.stock-alerts._row', ['alerte' => $alerte])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="tab-pane fade" id="resolues" role="tabpanel">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Produit</th>
                                <th>Stock</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertes->filter(fn($a) => $a->resolue) as $alerte)
                                @include('admin.stock-alerts._row', ['alerte' => $alerte])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="tab-pane fade" id="toutes" role="tabpanel">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Produit</th>
                                <th>Stock</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.stock-alerts._rows', ['alertes' => $alertes])
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $alertes->links() }}
    </div>
</div>
@endsection
