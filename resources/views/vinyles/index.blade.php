<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Catalogue des vinyles</h2>

            @if (request('filter') === 'stock_bas')
                <span class="badge badge-warning">⚠️ Stock bas uniquement</span>
            @elseif(request('filter') === 'rupture')
                <span class="badge badge-danger">🚨 Ruptures de stock</span>
            @endif
        </div>

         <a href="{{ route('vinyles.create') }}" class="btn btn-primary">
            + Nouveau vinyle
        </a>
    </x-slot>

    <div class="page-content" x-data="{ showModal: false, selectedVinyle: '', selectedId: null, confirmDelete(id, nom) { this.selectedId = id; this.selectedVinyle = nom; this.showModal = true; }, deleteVinyle() { if (this.selectedId) { window.location.href = '/vinyles/' + this.selectedId; } } }">
        <form method="GET" action="{{ route('vinyles.index') }}" class="search-box">
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom ou modèle..."
                class="search-input">

            @if ($search)
                <a href="{{ route('vinyles.index') }}" class="btn btn-secondary" style="margin-left: 8px;">
                    Réinitialiser
                </a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="vinyle-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Modèle</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vinyles as $vinyle)
                        <tr class="{{ $vinyle->isLowStock() ? 'low-stock' : '' }}">
                            <td>
                                @if ($vinyle->hasMedia('photo'))
                                    <img src="{{ $vinyle->getFirstMediaUrl('photo', 'thumb') }}"
                                        alt="{{ $vinyle->nom }}" class="thumb-img">
                                @else
                                    <div class="no-image">Pas d'image</div>
                                @endif

                            </td>
                            <td>{{ $vinyle->nom }}</td>
                            <td>{{ $vinyle->modele }}</td>
                            <td>{{ number_format($vinyle->prix, 2) }} €</td>
                            <td>
                                <span class="badge {{ $vinyle->isLowStock() ? 'badge-danger' : 'badge-success' }}">
                                    {{ $vinyle->quantite }}
                                </span>
                            </td>
                            <td class="actions">
                                <a href="{{ route('vinyles.edit', $vinyle) }}" class="btn btn-sm btn-secondary">
                                    Éditer
                                </a>
                                <button @click="confirmDelete({{ $vinyle->id }}, '{{ addslashes($vinyle->nom) }}')"
                                    class="btn btn-sm btn-danger">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun vinyle trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $vinyles->links() }}
        </div>

        <!-- Modal de confirmation -->
        <div x-show="showModal" x-cloak class="modal" @click.away="showModal = false">
            <div class="modal-content" @click.stop>
                <h3>Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer le vinyle <strong x-text="selectedVinyle"></strong> ?</p>
                <div class="modal-actions">
                    <button @click="showModal = false" class="btn btn-secondary">Annuler</button>
                    <button @click="deleteVinyle()" class="btn btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
