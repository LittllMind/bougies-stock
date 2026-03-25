<!-- Catalogue revisité avec style artisanal -->
@extends('layouts.bougies')

@section('title', 'Catalogue — Bougies Artisanales Sculptées')

@section('content')
<div id="catalogue-app" class="catalogue-container">
    {{-- Injection données serveur --}}
    <script>
        window.catalogueData = {
            bougies: @json($bougies),
            parfums: @json($parfums),
            collections: @json($collections)
        };
    </script>
    
    {{-- Hero section --}}
    <section class="catalogue-hero">
        <div class="hero-content">
            <h1>Bougies Sculptées <span class="accent">Artisanales</span></h1>
            <p class="hero-text">Chaque bougie est façonnée à la main, <br>unique en son genre.</p>
        </div>
    </section>
    
    {{-- Section produits --}}
    <section class="catalogue-section">
        <div class="catalogue-layout">
            {{-- Sidebar filtres --}}
            <aside class="filters-sidebar">
                <h2 class="filters-title">Filtrer</h2>
                
                <div class="filter-group">
                    <label for="filter-parfum">Parfum</label>
                    <select id="filter-parfum" v-model="filtreParfum" @change="appliquerFiltres">
                        <option value="">Tous les parfums</option>
                        <option v-for="parfum in parfums" :value="parfum" :key="parfum">
                            @{{ parfum }}
                        </option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter-collection">Collection</label>
                    <select id="filter-collection" v-model="filtreCollection" @change="appliquerFiltres">
                        <option value="">Toutes les collections</option>
                        <option v-for="collection in collections" :value="collection" :key="collection">
                            @{{ collection }}
                        </option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Prix maximum : @{{ prixMax }}€</label>
                    <input 
                        type="range" 
                        v-model.number="prixMax" 
                        :min="prixMin" 
                        :max="prixMaxGlobal"
                        @input="appliquerFiltres"
                        class="prix-range"
                    >
                    <div class="range-labels">
                        <span>@{{ prixMin }}€</span>
                        <span>@{{ prixMaxGlobal }}€</span>
                    </div>
                </div>
                
                <button class="btn-outline btn-full" @click="resetFiltres">
                    Réinitialiser
                </button>
            </aside>
            
            {{-- Grille produits --}}
            <div class="products-area">
                <div class="products-header">
                    <p class="results-count">@{{ bougiesFiltrees.length }} bougie(s)</p>
                    
                    <select v-model="tri" @change="appliquerFiltres" class="sort-select">
                        <option value="newest">Nouveautés</option>
                        <option value="price-asc">Prix croissant</option>
                        <option value="price-desc">Prix décroissant</option>
                    </select>
                </div>
                
                {{-- Grille --}}
                <div v-if="bougiesFiltrees.length > 0" class="products-grid">
                    <bougie-card 
                        v-for="bougie in bougiesFiltrees" 
                        :key="bougie.id"
                        :bougie="bougie"
                        @add-to-cart="ajouterAuPanier"
                    ></bougie-card>
                </div>
                
                {{-- Message vide --}}
                <div v-else class="empty-state">
                    <div class="empty-icon">🕯️</div>
                    <h3>Aucune bougie ne correspond</h3>
                    <p>Essayez de modifier vos filtres</p>
                    <button class="btn-primary" @click="resetFiltres">
                        Voir tout le catalogue
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Hero section */
    .catalogue-hero {
        background: var(--cream-dark);
        padding: 4rem 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(201, 169, 98, 0.15);
    }
    
    .hero-content h1 {
        font-family: var(--font-serif);
        font-size: 3rem;
        font-weight: 400;
        margin-bottom: 1rem;
        letter-spacing: 0.02em;
    }
    
    .hero-content h1 .accent {
        color: var(--terracotta);
        font-style: italic;
    }
    
    .hero-text {
        font-size: 1.1rem;
        color: var(--warm-gray);
        font-weight: 300;
    }
    
    /* Layout principal */
    .catalogue-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .catalogue-section {
        padding: 3rem 2rem;
    }
    
    .catalogue-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 3rem;
    }
    
    /* Sidebar filtres */
    .filters-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    
    .filters-title {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--gold);
    }
    
    .filter-group {
        margin-bottom: 1.5rem;
    }
    
    .filter-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--charcoal);
    }
    
    .filter-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(201, 169, 98, 0.3);
        border-radius: 4px;
        background: white;
        font-family: var(--font-body);
        font-size: 0.9rem;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    
    .filter-group select:focus {
        outline: none;
        border-color: var(--gold);
    }
    
    .prix-range {
        width: 100%;
        margin: 0.5rem 0;
    }
    
    .range-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: var(--warm-gray);
    }
    
    .btn-full {
        width: 100%;
        justify-content: center;
    }
    
    /* Zone produits */
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(201, 169, 98, 0.15);
    }
    
    .results-count {
        color: var(--warm-gray);
        font-size: 0.9rem;
    }
    
    .sort-select {
        padding: 0.5rem 1rem;
        border: 1px solid rgba(201, 169, 98, 0.3);
        border-radius: 4px;
        background: white;
        font-family: var(--font-body);
        font-size: 0.85rem;
        cursor: pointer;
    }
    
    .sort-select:focus {
        outline: none;
        border-color: var(--gold);
    }
    
    /* Grille produits */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .empty-state h3 {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: var(--warm-gray);
        margin-bottom: 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .catalogue-layout {
            grid-template-columns: 1fr;
        }
        
        .filters-sidebar {
            position: static;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .filters-title {
            grid-column: 1 / -1;
        }
        
        .hero-content h1 {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
        
        .hero-content h1 {
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    const { createApp, ref, computed, onMounted } = Vue;
    
    // Composant carte bougie
    const BougieCard = {
        props: ['bougie'],
        emits: ['add-to-cart'],
        template: `
            <article class="bougie-card">
                <a :href="'/catalogue/' + bougie.reference" class="card-link">
                    <div class="card-image">
                        <img :src="bougie.image_url || '/images/bougie-placeholder.jpg'" 
                             :alt="bougie.nom"
                             loading="lazy"
                        >
                        <div v-if="bougie.quantite <= 0" class="stock-badge epuise">
                            Épuisé
                        </div>
                        <div v-else-if="bougie.quantite <= 3" class="stock-badge faible">
                            Derniers exemplaires
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <p class="card-collection">@{{ bougie.collection }}</p>
                        <h3 class="card-title">@{{ bougie.nom }}</h3>
                        <p class="card-parfum">@{{ bougie.parfum }} · @{{ bougie.format }}</p>
                        
                        <div class="card-footer">
                            <span class="card-price">@{{ formatPrix(bougie.prix) }}</span>
                            <button 
                                class="card-btn"
                                :disabled="bougie.quantite <= 0"
                                @click.stop.prevent="$emit('add-to-cart', bougie)"
                            >
                                @{{ bougie.quantite > 0 ? 'Ajouter' : 'Indisponible' }}
                            </button>
                        </div>
                    </div>
                </a>
            </article>
        `,
        methods: {
            formatPrix(prix) {
                return new Intl.NumberFormat('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(prix);
            }
        }
    };
    
    createApp({
        components: { BougieCard },
        setup() {
            // Données initiales depuis le serveur
            const data = window.catalogueData || { bougies: [], parfums: [], collections: [] };
            
            const bougies = ref(data.bougies);
            const parfums = ref(data.parfums);
            const collections = ref(data.collections);
            
            // Filtres
            const filtreParfum = ref('');
            const filtreCollection = ref('');
            const prixMax = ref(50);
            const prixMin = ref(15);
            const prixMaxGlobal = ref(45);
            const tri = ref('newest');
            
            // Filtrage et tri
            const bougiesFiltrees = computed(() => {
                // Filtrer les bougies en stock
                let result = bougies.value.filter(b => b.quantite > 0);
                
                // Filtre parfum
                if (filtreParfum.value) {
                    result = result.filter(b => b.parfum === filtreParfum.value);
                }
                
                // Filtre collection
                if (filtreCollection.value) {
                    result = result.filter(b => b.collection === filtreCollection.value);
                }
                
                // Filtre prix
                result = result.filter(b => parseFloat(b.prix) <= prixMax.value);
                
                // Tri
                switch (tri.value) {
                    case 'price-asc':
                        result.sort((a, b) => parseFloat(a.prix) - parseFloat(b.prix));
                        break;
                    case 'price-desc':
                        result.sort((a, b) => parseFloat(b.prix) - parseFloat(a.prix));
                        break;
                    case 'newest':
                    default:
                        // Tri par ID décroissant (supposant que ID = auto-increment)
                        result.sort((a, b) => b.id - a.id);
                }
                
                return result;
            });
            
            function appliquerFiltres() {
                // Les filtres sont reactifs via computed
            }
            
            function resetFiltres() {
                filtreParfum.value = '';
                filtreCollection.value = '';
                prixMax.value = prixMaxGlobal.value;
                tri.value = 'newest';
            }
            
            function ajouterAuPanier(bougie) {
                // Redirection vers le panier avec paramètre
                window.location.href = `/cart/add?bougie_id=${bougie.id}&quantite=1`;
            }
            
            return {
                bougies,
                parfums,
                collections,
                filtreParfum,
                filtreCollection,
                prixMax,
                prixMin,
                prixMaxGlobal,
                tri,
                bougiesFiltrees,
                appliquerFiltres,
                resetFiltres,
                ajouterAuPanier
            };
        }
    }).mount('#catalogue-app');
</script>

<style>
    /* Carte Bougie Styles */
    .bougie-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(201, 169, 98, 0.1);
    }
    
    .bougie-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }
    
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .card-image {
        position: relative;
        aspect-ratio: 3/4;
        background: var(--cream-dark);
        overflow: hidden;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .bougie-card:hover .card-image img {
        transform: scale(1.03);
    }
    
    .stock-badge {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    
    .stock-badge.epuise {
        background: var(--charcoal);
        color: white;
    }
    
    .stock-badge.faible {
        background: var(--terracotta);
        color: white;
    }
    
    .card-content {
        padding: 1.25rem;
    }
    
    .card-collection {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gold);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .card-title {
        font-family: var(--font-serif);
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--charcoal);
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }
    
    .card-parfum {
        font-size: 0.85rem;
        color: var(--warm-gray);
        margin-bottom: 1rem;
    }
    
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid rgba(201, 169, 98, 0.15);
    }
    
    .card-price {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--charcoal);
    }
    
    .card-btn {
        padding: 0.5rem 1rem;
        background: var(--gold);
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .card-btn:hover:not(:disabled) {
        background: var(--gold-light);
    }
    
    .card-btn:disabled {
        background: var(--cream-dark);
        color: var(--warm-gray);
        cursor: not-allowed;
    }
</style>
@endpush
