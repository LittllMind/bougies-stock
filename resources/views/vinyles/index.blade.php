<x-app-layout>
    <x-slot name="header">
        <div class="header-actions">
            <h2>Gestion des Vinyles</h2>
            <a href="{{ route('vinyles.create') }}" class="btn btn-primary">+ Ajouter un vinyle</a>
        </div>
    </x-slot>

    <div x-data="vinyleSearch()" class="page-content">
        <div class="search-box">
            <input 
                type="text" 
                x-model="search" 
                placeholder="Rechercher par nom ou modèle..."
                class="search-input"
            >
        </div>

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
                        <tr class="{{ $vinyle->isLowStock() ? 'low-stock' : '' }}" 
                            x-show="filterVinyle('{{ $vinyle->nom }}', '{{ $vinyle->modele }}')">
                            <td>
                                @if($vinyle->hasMedia('photos'))
                                    <img src="{{ $vinyle->getFirstMediaUrl('photos', 'thumb') }}" 
                                         alt="{{ $vinyle->nom }}"
                                         class="thumb-img">
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
                                <button 
                                    @click="confirmDelete({{ $vinyle->id }}, '{{ $vinyle->nom }}')"
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
        <div x-show="showModal" 
             x-cloak
             class="modal"
             @click.away="showModal = false">
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

    <script>
        function vinyleSearch() {
            return {
                search: '',
                showModal: false,
                selectedId: null,
                selectedVinyle: '',
                
                filterVinyle(nom, modele) {
                    if (this.search === '') return true;
                    const searchLower = this.search.toLowerCase();
                    return nom.toLowerCase().includes(searchLower) || 
                           modele.toLowerCase().includes(searchLower);
                },
                
                confirmDelete(id, nom) {
                    this.selectedId = id;
                    this.selectedVinyle = nom;
                    this.showModal = true;
                },
                
                deleteVinyle() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/vinyles/${this.selectedId}`;
                    
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfField);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
