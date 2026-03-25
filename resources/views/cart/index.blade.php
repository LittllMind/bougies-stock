@extends('layouts.app')

@section('title', 'Mon Panier - Bougies Artisanales')

@section('content')
<div id="cart-app" class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-amber-800">
            🛒 Mon Panier
        </h1>
        <a href="/catalogue" class="text-amber-600 hover:text-amber-700 transition">
            ← Continuer mes achats
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Panier vide --}}
            <div v-if="!items.length" class="bg-amber-50 border border-amber-200 overflow-hidden rounded-xl">
                <div class="p-6 text-center">
                    <div class="text-6xl mb-4">🛒</div>
                    <h3 class="text-xl font-semibold text-amber-800 mb-2">
                        Votre panier est vide
                    </h3>
                    <p class="text-amber-600 mb-6">
                        Découvrez notre sélection de bougies artisanales
                    </p>
                    <a href="/catalogue"
                        class="inline-block bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 transition font-semibold">
                        Voir les bougies
                    </a>
                </div>
            </div>

            {{-- Panier avec articles --}}
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Colonne principale : Liste des articles --}}
                <div class="lg:col-span-2">
                    <div class="bg-white border border-amber-200 overflow-hidden rounded-xl shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-amber-800 mb-4">
                                Articles (@{{ itemsCount }})
                            </h3>

                            <div class="space-y-4">
                                <div v-for="item in items" :key="item.reference" 
                                     class="flex justify-between py-3 border-b border-amber-100 last:border-0">
                                    <div>
                                        <div class="font-bold text-gray-800 text-lg">
                                            @{{ item.nom }}
                                        </div>

                                        <div class="text-sm text-amber-600 mt-1">
                                            Parfum : @{{ item.parfum || 'Standard' }}
                                        </div>

                                        <div class="flex items-center gap-2 mt-2">
                                            <label class="text-sm text-gray-600">Qté:</label>
                                            <select v-model="item.quantite" 
                                                    @change="updateQuantity(item)"
                                                    class="border border-amber-300 rounded px-2 py-1 text-sm">
                                                <option v-for="n in 10" :key="n" :value="n">@{{ n }}</option>
                                            </select>
                                            <button @click="removeItem(item)" 
                                                    class="text-red-500 hover:text-red-700 text-sm ml-4">
                                                ✕ Supprimer
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-gray-500 text-sm">@{{ formatPrice(item.prix_unitaire) }} € / u</div>
                                        <div class="font-bold text-amber-600 text-lg">
                                            @{{ formatPrice(item.sous_total) }} €
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Vider le panier --}}
                            <div class="mt-6 pt-4 border-t border-amber-100">
                                <button @click="clearCart" 
                                        class="text-red-500 hover:text-red-700 text-sm font-semibold transition">
                                    🗑️ Vider le panier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colonne latérale : Récapitulatif --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-amber-200 overflow-hidden rounded-xl shadow-sm sticky top-4">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-amber-800 mb-4">
                                Récapitulatif
                            </h3>

                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Articles</span>
                                    <span class="font-medium">@{{ itemsCount }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Sous-total</span>
                                    <span class="font-medium">@{{ formatPrice(total) }} €</span>
                                </div>
                            </div>

                            <div class="border-t border-amber-200 pt-4 mb-6">
                                <div class="flex justify-between text-xl font-bold text-amber-800">
                                    <span>Total</span>
                                    <span>@{{ formatPrice(total) }} €</span>
                                </div>
                            </div>

                            <a href="/orders/create"
                               class="block w-full bg-amber-600 hover:bg-amber-700 text-white text-center py-3 rounded-lg font-semibold transition">
                                Valider ma commande
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/cart.js"></script>
@endsection
