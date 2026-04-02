@extends('layouts.admin')

@section('title', 'Détail Alerte #' . $stockAlert->id)

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.stock-alerts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour aux alertes
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header {{ $stockAlert->resolue ? 'bg-success' : 'bg-warning' }} text-white">
                    <h5 class="mb-0">
                        @if($stockAlert->resolue)
                            ✅ Alerte Résolue
                        @else
                            🚨 Alerte Active
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Produit</h6>
                            <p class="mb-0">
                                <strong>{{ $stockAlert->stockable->nom }}</strong><br>
                                <small class="text-muted">{{ $stockAlert->stockable->reference }}</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Type</h6>
                            <p class="mb-0">{{ class_basename($stockAlert->stockable_type) }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Quantité Actuelle</h6>
                            <span class="badge bg-danger fs-4">{{ $stockAlert->quantite_actuelle }}</span>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Seuil Critique</h6>
                            <span class="badge bg-secondary fs-4">{{ $stockAlert->seuil_alerte }}</span>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Déficit</h6>
                            <span class="badge bg-warning fs-4">{{ max(0, $stockAlert->seuil_alerte - $stockAlert->quantite_actuelle + 1) }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Créée le</h6>
                            <p>{{ $stockAlert->created_at->format('d/m/Y à H:i') }} 
                               ({{ $stockAlert->created_at->diffForHumans() }})</p>
                        </div>
                        @if($stockAlert->resolue)
                        <div class="col-md-6">
                            <h6 class="text-muted">Résolue le</h6>
                            <p>{{ $stockAlert->resolved_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    @if(!$stockAlert->resolue)
                        <form action="{{ route('admin.stock-alerts.resolve', $stockAlert) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg"></i> Marquer comme résolue
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.bougies.edit', $stockAlert->stockable) }}" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-seam"></i> Gérer le stock produit
                    </a>

                    <form action="{{ route('admin.stock-alerts.destroy', $stockAlert) }}" method="POST"
                          onsubmit="return confirm('Confirmer la suppression ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> Supprimer l'alerte
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
