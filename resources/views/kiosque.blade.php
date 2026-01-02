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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="w-full sm:max-w-md">
                <input type="text" x-model="search" placeholder="Rechercher par nom ou modèle..." class="form-input w-full" />
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showAll = !showAll" class="btn btn-secondary">
                    <span x-text="showAll ? 'Masquer rupture de stock' : 'Afficher tous'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template x-for="vinyle in filteredVinyles" :key="vinyle.id">
                <div class="bg-white rounded shadow hover:shadow-md overflow-hidden">
                    <div class="w-full h-48 bg-gray-100">
                        <img :src="vinyle.image_standard || '/images/no-image.png'" :alt="vinyle.nom" class="w-full h-full object-cover" />
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-semibold truncate" x-text="vinyle.nom"></h3>
                        <p class="text-xs text-gray-500" x-text="vinyle.modele"></p>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-indigo-600 font-bold" x-text="formatPrice(vinyle.prix)"></div>
                            <div class="text-gray-500 text-sm" x-text="`Stock: ${vinyle.quantite ?? 0}`"></div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-primary w-full" @click.stop="openQuantityModal(vinyle)" :disabled="(vinyle.quantite ?? 0) <= 0">
                                <span x-show="(vinyle.quantite ?? 0) > 0">Ajouter au panier</span>
                                <span x-show="(vinyle.quantite ?? 0) <= 0">Rupture de stock</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="fixed inset-x-0 bottom-4 flex justify-center sm:hidden">
            <a href="{{ route('cart.index') }}" class="btn btn-primary w-11/12">🛒 Voir mon panier ({{ $cartCount }})</a>
        </div>

        <div x-show="selectedVinyle" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div @click.away="closeQuantityModal()" class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold" x-text="selectedVinyle.nom"></h3>

                <div class="text-center my-4">
                    <img :src="currentImageUrl()" :alt="selectedVinyle.nom" class="mx-auto max-h-56 object-contain" />
                </div>

                <p class="text-sm text-gray-500" x-text="selectedVinyle.modele"></p>

                <div class="flex items-center justify-center gap-3 my-3">
                    <button @click="decrementQuantity()" class="btn btn-secondary">-</button>
                    <div class="text-lg font-bold" x-text="selectedQuantity"></div>
                    <button @click="incrementQuantity()" class="btn btn-secondary">+</button>
                </div>

                <div class="mt-2">
                    <label for="fond" class="block text-sm">Fond</label>
                    <select id="fond" x-model="selectedFond" class="form-input mt-1 w-full">
                        <option value="standard">Standard (sans supplément)</option>
                        <option value="miroir">Fond miroir (+8 €)</option>
                        <option value="dore">Fond doré (+13 €)</option>
                    </select>
                </div>

                <div class="mt-3 text-lg font-bold" x-text="formatPrice(currentUnitPrice())"></div>

                <div class="flex justify-end gap-2 mt-4">
                    <button @click="closeQuantityModal()" class="btn btn-secondary">Annuler</button>
                    <button @click="submitCart()" class="btn btn-primary">Ajouter</button>
                </div>

                <form x-ref="addToCartForm" action="{{ route('cart.add') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="vinyle_id" x-ref="vinyleId">
                    <input type="hidden" name="quantite" x-ref="quantite">
                    <input type="hidden" name="fond" x-ref="fond"> 
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine component --}}
    <script>
        function kiosqueComponent(vinylesFromPhp) {
            return {
                vinyles: vinylesFromPhp,
                search: '',
                showAll: false,

                selectedVinyle: null,
                selectedQuantity: 1,
                selectedFond: 'standard',

                get filteredVinyles() {
                    const s = (this.search || '').toLowerCase();
                    return this.vinyles.filter(v => {
                        const matchesSearch = (v.nom || '').toLowerCase().includes(s) || (v.modele || '').toLowerCase().includes(s);
                        const inStock = this.showAll || (v.quantite ?? 0) > 0;
                        return matchesSearch && inStock;
                    });
                },

                openQuantityModal(vinyle) {
                    if ((vinyle.quantite ?? 0) <= 0) return;
                    this.selectedVinyle = vinyle;
                    this.selectedQuantity = 1;
                    this.selectedFond = 'standard';
                },
                closeQuantityModal() {
                    this.selectedVinyle = null;
                    this.selectedQuantity = 1;
                    this.selectedFond = 'standard';
                },
                incrementQuantity() {
                    if (!this.selectedVinyle) return;
                    const max = (this.selectedVinyle.quantite ?? 0);
                    if (this.selectedQuantity < max) this.selectedQuantity++;
                },
                decrementQuantity() {
                    if (this.selectedQuantity > 1) this.selectedQuantity--;
                },
                currentImageUrl() {
                    if (!this.selectedVinyle) return '/images/no-image.png';
                    if (this.selectedFond === 'miroir' && this.selectedVinyle.image_miroir) return this.selectedVinyle.image_miroir;
                    if (this.selectedFond === 'dore' && this.selectedVinyle.image_dore) return this.selectedVinyle.image_dore;
                    return this.selectedVinyle.image_standard || '/images/no-image.png';
                },
                currentUnitPrice() {
                    if (!this.selectedVinyle) return 0;
                    const base = Number(this.selectedVinyle.prix || 0);
                    const supplement = this.selectedFond === 'miroir' ? 8 : (this.selectedFond === 'dore' ? 13 : 0);
                    return base + supplement;
                },
                formatPrice(amount) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(Number(amount || 0));
                },
                submitCart() {
                    if (!this.selectedVinyle) return;

                    this.$refs.vinyleId.value = this.selectedVinyle.id;
                    this.$refs.quantite.value = this.selectedQuantity;
                    this.$refs.fond.value = this.selectedFond; // ⚠️ IMPORTANT

                    this.$refs.addToCartForm.submit();
                },

            }
        }
    </script>
</x-app-layout>
