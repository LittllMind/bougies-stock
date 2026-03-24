<?php

/**
 * Vue Catalogue - Page d'accueil du site
 * Affiche les bougies en grille avec filtres Vue.js
 */
?>
@extends('layouts.app')

@section('title', 'Catalogue - Bougies Artisanales')

@section('content')
<div class="container py-5" id="catalogue-app">
    <!-- Injecter les données serveur pour Vue.js -->
    <script>
        window.initialBougies = @json($bougies);
        window.initialParfums = @json($parfums);
        window.initialCollections = @json($collections);
    </script>

    <div class="row">
        <!-- Sidebar Filtres -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <!-- Parfum -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parfum</label>
                        <select class="form-select" v-model="filtreParfum">
                            <option value="">Tous les parfums</option>
                            <option v-for="parfum in parfums" :key="parfum" :value="parfum">
                                @{{ parfum }}
                            </option>
                        </select>
                    </div>

                    <!-- Collection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Collection</label>
                        <select class="form-select" v-model="filtreCollection">
                            <option value="">Toutes les collections</option>
                            <option v-for="collection in collections" :key="collection" :value="collection">
                                @{{ collection }}
                            </option>
                        </select>
                    </div>

                    <!-- Prix -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Prix maximum: @{{ prixMax }}€</label>
                        <input type="range" class="form-range" v-model.number="prixMax"
                               :min="prixMin" :max="prixMaxGlobal" step="1">
                        <div class="d-flex justify-content-between">
                            <span>@{{ prixMin }}€</span>
                            <span>@{{ prixMaxGlobal }}€</span>
                        </div>
                    </div>

                    <!-- Reset -->
                    <button class="btn btn-outline-secondary w-100" @click="resetFiltres">
                        Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Grille Produits -->
        <div class="col-md-9">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Nos Bougies Artisanales</h2>
                <div>
                    <span class="text-muted">@{{ bougiesFiltrees.length }} résultat(s)</span>
                </div>
            </div>

            <!-- Tri -->
            <div class="mb-3">
                <label class="form-label">Trier par:</label>
                <select class="form-select w-auto d-inline-block" v-model="tri">
                    <option value="created_at-desc">Nouveautés</option>
                    <option value="prix-asc">Prix croissant</option>
                    <option value="prix-desc">Prix décroissant</option>
                    <option value="nom-asc">Nom A-Z</option>
                </select>
            </div>

            <!-- Grille -->
            <div class="row" v-if="bougiesFiltrees.length > 0">
                <div v-for="bougie in bougiesFiltrees" :key="bougie.id" class="col-md-4 mb-4">
                    <bougie-card :bougie="bougie" @add-to-cart="ajouterAuPanier">
                    </bougie-card>
                </div>
            </div>

            <!-- Message si aucun résultat -->
            <div v-else class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Aucune bougie ne correspond à vos critères.
                <button class="btn btn-link" @click="resetFiltres">Réinitialiser les filtres</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @env('testing')
        {{-- Mode test: chargement direct du fichier --}}
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script>
            // Minimal Vue app pour les tests
            const { createApp, ref, computed } = Vue;
            
            // Composant BougieCard inline
            const BougieCard = {
                props: ['bougie'],
                template: `
                    <div class="card h-100">
                        <div class="card-img-top bg-secondary" style="height: 150px;"></div>
                        <div class="card-body">
                            <h5 class="card-title" v-text="bougie.nom"></h5>
                            <p class="card-text text-muted" v-text="bougie.collection"></p>
                            <p class="card-text" v-text="bougie.parfum"></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0" v-text="bougie.prix + '€'"></span>
                                <button class="btn btn-primary" @click="$emit('add-to-cart', bougie.id)">
                                    <i class="bi bi-cart-plus"></i> Ajouter
                                </button>
                            </div>
                        </div>
                    </div>
                `
            };
            
            createApp({
                components: { BougieCard },
                setup() {
                    const bougies = ref(window.initialBougies || []);
                    const parfums = ref(window.initialParfums || []);
                    const collections = ref(window.initialCollections || []);
                    const filtreParfum = ref('');
                    const filtreCollection = ref('');
                    const prixMax = ref(100);
                    const prixMin = ref(0);
                    const prixMaxGlobal = ref(100);
                    const tri = ref('created_at-desc');
                    
                    const bougiesFiltrees = computed(() => {
                        let result = bougies.value.filter(b => b.quantite > 0);
                        
                        if (filtreParfum.value) {
                            result = result.filter(b => b.parfum === filtreParfum.value);
                        }
                        if (filtreCollection.value) {
                            result = result.filter(b => b.collection === filtreCollection.value);
                        }
                        if (prixMax.value) {
                            result = result.filter(b => parseFloat(b.prix) <= prixMax.value);
                        }
                        
                        // Tri
                        const [champ, ordre] = tri.value.split('-');
                        result.sort((a, b) => {
                            let valA = a[champ];
                            let valB = b[champ];
                            if (champ === 'prix') {
                                valA = parseFloat(valA);
                                valB = parseFloat(valB);
                            }
                            if (ordre === 'asc') return valA > valB ? 1 : -1;
                            return valA < valB ? 1 : -1;
                        });
                        
                        return result;
                    });
                    
                    function resetFiltres() {
                        filtreParfum.value = '';
                        filtreCollection.value = '';
                        prixMax.value = prixMaxGlobal.value;
                    }
                    
                    function ajouterAuPanier(bougieId) {
                        console.log('Ajout au panier:', bougieId);
                    }
                    
                    return {
                        bougies, parfums, collections,
                        filtreParfum, filtreCollection, prixMax, prixMin, prixMaxGlobal, tri,
                        bougiesFiltrees, resetFiltres, ajouterAuPanier
                    };
                }
            }).mount('#catalogue-app');
        </script>
    @else
        {{-- Mode dev/prod: chargement via Vite --}}
        @vite(['resources/js/catalogue.js'])
    @endenv
@endsection
