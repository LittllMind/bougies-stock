@forelse($alertes as $alerte)
    <tr class="{{ $alerte->resolue ? 'table-success' : 'table-warning' }}">
        <td>
            <span class="badge {{ $alerte->resolue ? 'bg-success' : 'bg-warning' }}">
                {{ $alerte->resolue ? '✅ Résolue' : '🚨 En attente' }}
            </span>
        </td>
        <td>
            <strong>{{ $alerte->stockable->nom ?? 'Produit inconnu' }}</strong>
            <br>
            <small class="text-muted">{{ $alerte->stockable->reference ?? '-' }}</small>
        </td>
        <td>
            <span class="badge bg-danger">{{ $alerte->quantite_actuelle }} restant</span>
            <span class="text-muted">/ seuil: {{ $alerte->seuil_critique }}</span>
        </td>
        <td>
            <small>{{ $alerte->created_at->format('d/m/Y H:i') }}</small>
            <br>
            <small class="text-muted">{{ $alerte->created_at->diffForHumans() }}</small>
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.stock-alerts.show', $alerte) }}" 
                   class="btn btn-outline-info" title="Voir détails">
                    <i class="bi bi-eye"></i>
                </a>
                
                @if(!$alerte->resolue)
                    <form action="{{ route('admin.stock-alerts.resolve', $alerte) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success" title="Marquer comme résolue">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                @endif
                
                <form action="{{ route('admin.stock-alerts.destroy', $alerte) }}" 
                      method="POST" class="d-inline" 
                      onsubmit="return confirm('Supprimer cette alerte ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">
            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
            Aucune alerte dans cette catégorie
        </td>
    </tr>
@endforelse
