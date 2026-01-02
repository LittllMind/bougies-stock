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


    <div x-data="kiosqueComponent(@js($vinylesData))" class="kiosque-container">
        <div class="kiosque-header" style="display:flex; flex-direction:column; gap:10px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0; font-size:22px;">🎵 Catalogue Vinyles</h2>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">🛒 Mon Panier ({{ $cartCount }})</a>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <input type="text" x-model="search" placeholder="Rechercher par nom ou modèle..." class="form-input" style="flex:1;" />
                <button type="button" @click="showAll = !showAll" class="btn btn-secondary">
                    <span x-text="showAll ? 'Masquer rupture de stock' : 'Afficher tous'"></span>
                </button>
            </div>
        </div>

        <!-- Grille des vinyles -->
        <div class="kiosque-grid" style="display:flex; flex-wrap:wrap; gap:12px;">
            <template x-for="vinyle in filteredVinyles" :key="vinyle.id">
                <div style="width:calc(25% - 12px); min-width:220px;">
                    <div class="kiosque-card card h-100" :class="{ 'opacity-60': (vinyle.quantite ?? 0) <= 0 }">
                        <img :src="vinyle.image_standard || '/images/no-image.png'" :alt="vinyle.nom" style="width:100%; height:200px; object-fit:cover;" @click="openQuantityModal(vinyle)" />
                        <div style="padding:10px;">
                            <h5 style="margin:0 0 6px 0; font-size:16px;" x-text="vinyle.nom"></h5>
                            <p style="margin:0 0 8px; color:#6b7280;" x-text="vinyle.modele"></p>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                                <div style="font-weight:bold; color:#4F46E5;" x-text="formatPrice(vinyle.prix)"></div>
                                <div style="color:#6b7280; font-size:0.9rem;" x-text="`Stock: ${vinyle.quantite ?? 0}`"></div>
                            </div>

                            <div style="margin-top:10px;">
                                <button type="button" class="btn btn-primary w-100" @click.stop="openQuantityModal(vinyle)" :disabled="(vinyle.quantite ?? 0) <= 0">
                                    <span x-show="(vinyle.quantite ?? 0) > 0">Ajouter au panier</span>
                                    <span x-show="(vinyle.quantite ?? 0) <= 0">Rupture de stock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Barre panier fixe en bas (lien vers panier serveur) -->
        <div style="position:fixed; left:0; right:0; bottom:12px; display:flex; justify-content:center; pointer-events:auto;">
            <a href="{{ route('cart.index') }}" class="btn btn-primary" style="padding:10px 18px; font-weight:600; display:flex; gap:12px; align-items:center;">
                🛒 Voir mon panier ({{ $cartCount }})
            </a>
        </div>

        {{-- MODAL quantité + fond --}}
        <div x-show="selectedVinyle" style="display:none;" class="modal-overlay">
            <div class="modal-content" @click.away="closeQuantityModal()">
                <h3 style="margin-top:0;" x-text="selectedVinyle.nom"></h3>

                <div style="text-align:center; margin-bottom:15px;">
                    <img :src="currentImageUrl()" :alt="selectedVinyle.nom" style="max-width:260px; max-height:260px; object-fit:contain;" />
                </div>

                <p style="margin:0 0 10px 0; color:#6b7280;" x-text="selectedVinyle.modele"></p>

                <div style="display:flex; gap:8px; align-items:center; justify-content:center; margin-top:8px;">
                    <button @click="decrementQuantity()" class="btn btn-secondary">-</button>
                    <div style="min-width:40px; text-align:center; font-weight:700; font-size:1.1rem;" x-text="selectedQuantity"></div>
                    <button @click="incrementQuantity()" class="btn btn-secondary">+</button>
                </div>

                <div style="margin-top:12px;">
                    <label for="fond">Fond</label>
                    <select id="fond" x-model="selectedFond" class="form-input" style="width:100%; margin-top:6px;">
                        <option value="standard">Standard (sans supplément)</option>
                        <option value="miroir">Fond miroir (+8 €)</option>
                        <option value="dore">Fond doré (+13 €)</option>
                    </select>
                </div>

                <div style="margin-top:12px; font-size:1.25rem; font-weight:700;">
                    <span x-text="formatPrice(currentUnitPrice())"></span>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px;">
                    <button @click="closeQuantityModal()" class="btn btn-secondary">Annuler</button>
                    <button @click="submitCart()" class="btn btn-primary">Ajouter</button>
                </div>

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

    <style>
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:90}
        .modal-content{background:#fff;padding:20px;border-radius:8px;max-width:520px;width:95%;box-shadow:0 10px 25px rgba(0,0,0,0.2)}
        .kiosque-card.opacity-60{opacity:0.55}
    </style>

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
