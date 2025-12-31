{{-- resources/views/kiosque.blade.php --}}

@php
    /** @var \App\Services\CartService $cartService */
    $cartService = app(\App\Services\CartService::class);
    $cart = $cartService->getCart();
    $cartCount = $cart->items->sum('quantite');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="header-actions flex items-center justify-between">
            <h2>🎵 Catalogue Vinyles</h2>
            <a href="{{ route('cart.index') }}" class="btn btn-primary">
                🛒 Mon Panier ({{ $cartCount }})
            </a>
        </div>
    </x-slot>


    <div x-data="kiosqueComponent(@js($vinylesData))" class="space-y-6">
        {{-- Grille des vinyles --}}
        <div class="row">
            <template x-for="vinyle in vinyles" :key="vinyle.id">
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100" @click="openQuantityModal(vinyle)">
                        <img :src="vinyle.image_standard" class="card-img-top" :alt="vinyle.nom">

                        <div class="card-body">
                            <h5 class="card-title" x-text="vinyle.nom"></h5>
                            <p class="card-text text-muted" x-text="vinyle.modele"></p>
                            <p class="card-text fw-bold text-primary" x-text="formatPrice(vinyle.prix)">
                            </p>
                            <p class="card-text text-muted small">
                                Stock : <span x-text="vinyle.quantite"></span>
                            </p>
                            <button type="button" class="btn btn-primary w-100 mt-2"
                                @click.stop="openQuantityModal(vinyle)" :disabled="vinyle.quantite <= 0">
                                <span x-show="vinyle.quantite > 0">Ajouter au panier</span>
                                <span x-show="vinyle.quantite <= 0">Rupture de stock</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>


        {{-- MODAL quantité + fond --}}
        <div x-show="selectedVinyle" style="display:none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6" @click.away="closeQuantityModal()">

                <template x-if="selectedVinyle">
                    <div>
                        <!-- Aperçu image -->
                        <div class="text-center mb-4">
                            <img :src="currentImageUrl()" :alt="selectedVinyle.nom" class="mx-auto"
                                style="max-width: 260px; max-height: 260px; object-fit: contain;">
                        </div>

                        <h3 class="text-xl font-semibold" x-text="selectedVinyle.nom"></h3>
                        <p class="text-gray-600" x-text="selectedVinyle.modele"></p>

                        <!-- Sélecteur de quantité -->
                        <div class="flex items-center justify-center gap-4 mt-4">
                            <button @click="decrementQuantity()" class="btn btn-secondary">-</button>
                            <span class="text-xl font-bold" x-text="selectedQuantity"></span>
                            <button @click="incrementQuantity()" class="btn btn-secondary">+</button>
                        </div>

                        <!-- Choix du fond -->
                        <div class="mt-4">
                            <label for="fond" class="block text-sm font-medium text-gray-700">
                                Fond
                            </label>
                            <select id="fond" x-model="selectedFond" class="mt-1 form-input w-full">
                                <option value="standard">Standard (sans supplément)</option>
                                <option value="miroir">Fond miroir (+8 €)</option>
                                <option value="dore">Fond doré (+13 €)</option>
                            </select>
                        </div>

                        <!-- Prix unitaire dynamique -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Prix unitaire
                            </label>
                            <div class="text-2xl font-bold text-indigo-600">
                                <span x-text="formatPrice(currentUnitPrice())"></span>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="mt-6 flex justify-end gap-2">
                            <button @click="closeQuantityModal()" class="btn btn-secondary">
                                Annuler
                            </button>
                            <button @click="submitCart()" class="btn btn-primary">
                                Ajouter
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Formulaire caché vers le vrai panier --}}
                <form x-ref="addToCartForm" action="{{ route('cart.add') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="vinyle_id" x-ref="vinyleId">
                    <input type="hidden" name="quantite" x-ref="quantite">
                    <input type="hidden" name="fond" x-ref="fond"> {{-- ⚠️ on remet juste ce champ caché --}}
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine component --}}
    <script>
        function kiosqueComponent(vinylesFromPhp) {
            return {
                vinyles: vinylesFromPhp,
                selectedVinyle: null,
                selectedQuantity: 1,
                selectedFond: 'standard',

                openQuantityModal(vinyle) {
                    if (vinyle.quantite <= 0) return;
                    this.selectedVinyle = vinyle;
                    this.selectedQuantity = 1;
                    this.selectedFond = 'standard';
                },
                closeQuantityModal() {
                    this.selectedVinyle = null;
                },
                incrementQuantity() {
                    if (!this.selectedVinyle) return;
                    if (this.selectedQuantity < this.selectedVinyle.quantite) {
                        this.selectedQuantity++;
                    }
                },
                decrementQuantity() {
                    if (this.selectedQuantity > 1) {
                        this.selectedQuantity--;
                    }
                },
                currentImageUrl() {
                    if (!this.selectedVinyle) return '';
                    if (this.selectedFond === 'miroir' && this.selectedVinyle.image_miroir) {
                        return this.selectedVinyle.image_miroir;
                    }
                    if (this.selectedFond === 'dore' && this.selectedVinyle.image_dore) {
                        return this.selectedVinyle.image_dore;
                    }
                    return this.selectedVinyle.image_standard;
                },
                currentUnitPrice() {
                    if (!this.selectedVinyle) return 0;
                    let base = Number(this.selectedVinyle.prix);
                    if (this.selectedFond === 'miroir') base += 8;
                    if (this.selectedFond === 'dore') base += 13;
                    return base;
                },
                formatPrice(amount) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(amount);
                },
                submitCart() {
                    this.$refs.vinyleId.value = this.selectedVinyle.id;
                    this.$refs.quantite.value = this.selectedQuantity;
                    this.$refs.fond.value = this.selectedFond; // ⚠️ IMPORTANT

                    this.$refs.addToCartForm.submit();
                },

            }
        }
    </script>
</x-app-layout>
