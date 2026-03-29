@extends('layouts.admin')

@section('title', 'Mode Marché - Vente Directe')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="marcheApp()" x-init="init()">
    
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">🎪 Mode Marché</h1>
            <p class="text-gray-600">Vente directe sur place</p>
        </div>
        <div class="text-right">
            <div class="text-sm text-gray-500">Ventes du jour</div>
            <div class="text-2xl font-bold text-[#D4AF37]" x-text="formatPrix(totalJour) + ' €'"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Colonne Catalogue -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="mb-4 flex gap-2">
                    <input type="text" x-model="search" placeholder="Rechercher une bougie..." 
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-2">
                    <select x-model="filtreCollection" class="border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">Toutes collections</option>
                        <option value="Spirit">Spirit</option>
                        <option value="Art">Art</option>
                        <option value="Nature">Nature</option>
                    </select>
                </div>

                <!-- Grille produits -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto">
                    <template x-for="bougie in filtrees" :key="bougie.id">
                        <div class="border rounded-lg p-3 cursor-pointer transition hover:shadow-md"
                             :class="bougie.quantite > 0 ? 'hover:border-[#D4AF37]' : 'opacity-50 cursor-not-allowed'"
                             @click="bougie.quantite > 0 && ajouterAuPanier(bougie)">
                            <img :src="bougie.image_url || '/images/placeholder-candle.jpg'" 
                                 :alt="bougie.nom" 
                                 class="w-full h-32 object-cover rounded mb-2">
                            <h3 class="font-semibold text-sm" x-text="bougie.nom"></h3>
                            <p class="text-xs text-gray-500" x-text="bougie.reference"></p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="font-bold text-[#D4AF37]" x-text="formatPrix(bougie.prix) + ' €'"></span>
                                <span class="text-xs px-2 py-1 rounded"
                                      :class="bougie.quantite > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="bougie.quantite + ' rest.'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Colonne Panier / Caisse -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-4 sticky top-4">
                <h2 class="text-lg font-bold mb-4">🛒 Panier</h2>

                <!-- Items du panier -->
                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    <template x-if="panier.length === 0">
                        <p class="text-gray-500 text-center py-8">Aucun article</p>
                    </template>
                    <template x-for="(item, index) in panier" :key="index">
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <div class="flex-1">
                                <div class="font-medium" x-text="item.nom"></div>
                                <div class="text-sm text-gray-500">
                                    <span x-text="item.quantite + ' x ' + formatPrix(item.prix) + ' €'"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold" x-text="formatPrix(item.total) + ' €'"></div>
                                <button @click="retirerDuPanier(index)" class="text-red-500 text-sm hover:text-red-700">✕</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Réduction -->
                <div class="mb-4" x-show="panier.length > 0">
                    <label class="text-sm text-gray-600">Réduction (€)</label>
                    <input type="number" x-model="reduction" min="0" 
                           class="w-full border rounded px-3 py-2"
                           @input="reduction = Math.max(0, Math.min(parseFloat($event.target.value) || 0, sousTotal))">
                </div>

                <!-- Total -->
                <div x-show="panier.length > 0" class="border-t pt-4 mb-4">
                    <div class="flex justify-between mb-2" x-show="reduction > 0">
                        <span>Sous-total:</span>
                        <span x-text="formatPrix(sousTotal) + ' €'"></span>
                    </div>
                    <div class="flex justify-between mb-2 text-red-600" x-show="reduction > 0">
                        <span>Réduction:</span>
                        <span>- <span x-text="formatPrix(reduction) + ' €'"></span></span>
                    </div>
                    <div class="flex justify-between text-xl font-bold">
                        <span>TOTAL:</span>
                        <span class="text-[#D4AF37]" x-text="formatPrix(total) + ' €'"></span>
                    </div>
                </div>

                <!-- Mode de paiement -->
                <div x-show="panier.length > 0" class="mb-4">
                    <label class="text-sm text-gray-600 block mb-2">Mode de paiement</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="modePaiement = 'cash'"
                                :class="modePaiement === 'cash' ? 'bg-[#D4AF37] text-white' : 'bg-gray-100'"
                                class="p-2 rounded text-sm font-medium transition">
                            💵 Espèces
                        </button>
                        <button type="button" @click="modePaiement = 'cb_terminal'"
                                :class="modePaiement === 'cb_terminal' ? 'bg-[#D4AF37] text-white' : 'bg-gray-100'"
                                class="p-2 rounded text-sm font-medium transition">
                            💳 CB Terminal
                        </button>
                        <button type="button" @click="modePaiement = 'cheque'"
                                :class="modePaiement === 'cheque' ? 'bg-[#D4AF37] text-white' : 'bg-gray-100'"
                                class="p-2 rounded text-sm font-medium transition">
                            📝 Chèque
                        </button>
                        <button type="button" @click="modePaiement = 'virement'"
                                :class="modePaiement === 'virement' ? 'bg-[#D4AF37] text-white' : 'bg-gray-100'"
                                class="p-2 rounded text-sm font-medium transition">
                            🏦 Virement
                        </button>
                    </div>
                </div>

                <!-- Client -->
                <div x-show="panier.length > 0" class="mb-4">
                    <label class="text-sm text-gray-600 block mb-2">Nom client (optionnel)</label>
                    <input type="text" x-model="nomClient" placeholder="Client"
                           class="w-full border rounded px-3 py-2">
                </div>

                <!-- Bouton valider -->
                <button x-show="panier.length > 0" 
                        @click="validerVente()"
                        :disabled="loading"
                        class="w-full bg-[#228B22] hover:bg-green-700 text-white font-bold py-3 rounded-lg transition disabled:opacity-50">
                    <span x-text="loading ? 'Traitement...' : '✅ VALIDER LA VENTE'"></span>
                </button>

                <button x-show="panier.length > 0" 
                        @click="viderPanier()"
                        class="w-full mt-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 rounded">
                    🗑️ Vider le panier
                </button>
            </div>
        </div>
    </div>
</div>

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function marcheApp() {
        return {
            bougies: @json($bougies),
            search: '',
            filtreCollection: '',
            panier: [],
            reduction: 0,
            modePaiement: 'cash',
            nomClient: '',
            loading: false,
            totalJour: 0,

            get filtrees() {
                return this.bougies.filter(b => {
                    const matchSearch = b.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                                      b.reference.toLowerCase().includes(this.search.toLowerCase());
                    const matchCollection = !this.filtreCollection || b.collection === this.filtreCollection;
                    return matchSearch && matchCollection;
                });
            },

            get sousTotal() {
                return this.panier.reduce((sum, item) => sum + item.total, 0);
            },

            get total() {
                return Math.max(0, this.sousTotal - (parseFloat(this.reduction) || 0));
            },

            init() {
                this.chargerTotalJour();
            },

            formatPrix(prix) {
                return parseFloat(prix).toFixed(2).replace('.', ',');
            },

            ajouterAuPanier(bougie) {
                const existant = this.panier.find(item => item.id === bougie.id);
                if (existant) {
                    if (existant.quantite < bougie.quantite) {
                        existant.quantite++;
                        existant.total = existant.quantite * existant.prix;
                    }
                } else {
                    this.panier.push({
                        id: bougie.id,
                        nom: bougie.nom,
                        reference: bougie.reference,
                        prix: bougie.prix,
                        quantite: 1,
                        total: bougie.prix
                    });
                }
            },

            retirerDuPanier(index) {
                this.panier.splice(index, 1);
            },

            viderPanier() {
                if (confirm('Vider le panier ?')) {
                    this.panier = [];
                    this.reduction = 0;
                }
            },

            async validerVente() {
                if (this.panier.length === 0) return;
                
                this.loading = true;
                
                const data = {
                    items: this.panier.map(item => ({
                        bougie_id: item.id,
                        quantite: item.quantite
                    })),
                    mode_paiement: this.modePaiement,
                    reduction: parseFloat(this.reduction) || 0,
                    affichage_client: this.nomClient || null
                };

                try {
                    const response = await fetch('/admin/marche/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert(`✅ Vente enregistrée !\nCommande: ${result.numero_commande}\nTotal: ${this.formatPrix(result.total)} €`);
                        this.viderPanier();
                        this.chargerTotalJour();
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + (result.message || result.errors ? JSON.stringify(result.errors) : 'Inconnue'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la vente. Vérifiez la console.');
                } finally {
                    this.loading = false;
                }
            },

            async chargerTotalJour() {
                try {
                    const response = await fetch('/admin/marche/ventes-jour?view=json');
                    const data = await response.json();
                    this.totalJour = data.total_jour || 0;
                } catch (e) {
                    console.error('Erreur chargement total:', e);
                }
            }
        }
    }
</script>
@endpush
@endsection
