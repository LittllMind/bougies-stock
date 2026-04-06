@extends('layouts.app')

@section('title', 'Paiement - Commande')

@section('content')
<div class="min-h-screen bg-amber-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-amber-900" style="font-family: 'Cormorant Garamond', serif;">
                Récapitulatif de commande
            </h1>
            <p class="mt-2 text-amber-600">Étape 3/3 : Paiement sécurisé</p>
        </div>

        <!-- Progression -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white text-sm">✓</div>
                    <span class="ml-2 text-green-700 text-sm">Panier</span>
                </div>
                <div class="w-16 h-1 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white text-sm">✓</div>
                    <span class="ml-2 text-green-700 text-sm">Livraison</span>
                </div>
                <div class="w-16 h-1 bg-amber-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white text-sm font-bold">3</div>
                    <span class="ml-2 text-amber-700 text-sm font-semibold">Paiement</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne gauche : Récapitulatif -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Adresse de livraison -->
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-amber-900 flex items-center">
                            <span class="text-2xl mr-2">📍</span> Adresse de livraison
                        </h2>
                        <a href="{{ route('orders.create') }}" class="text-sm text-amber-600 hover:text-amber-700 transition-colors">
                            Modifier
                        </a>
                    </div>
                    
                    <div class="space-y-2 text-amber-800">
                        <p class="font-semibold text-amber-900">{{ $shipping['nom'] }}</p>
                        <p>{{ $shipping['adresse'] }}</p>
                        <p>{{ $shipping['code_postal'] }} {{ $shipping['ville'] }}</p>
                        <p>{{ $shipping['pays'] === 'FR' ? 'France' : ($shipping['pays'] === 'BE' ? 'Belgique' : ($shipping['pays'] === 'CH' ? 'Suisse' : ($shipping['pays'] === 'LU' ? 'Luxembourg' : ($shipping['pays'] === 'DE' ? 'Allemagne' : 'Autre')))) }}</p>
                        <p class="text-sm text-amber-600 mt-2">
                            📧 {{ $shipping['email'] }} | 📱 {{ $shipping['telephone'] }}
                        </p>
                        @if(!empty($shipping['instructions']))
                            <p class="text-sm text-amber-600 mt-2 pt-2 border-t border-amber-200">
                                📝 Instructions : {{ $shipping['instructions'] }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Articles commandés -->
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200">
                    <h2 class="text-2xl font-bold text-amber-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">📦</span> Articles commandés
                    </h2>
                    
                    @if(count($items) > 0)
                        <div class="space-y-4">
                            @foreach($items as $item)
                            <div class="flex items-center space-x-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                                <!-- Image -->
                                <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                    </svg>
                                </div>
                                
                                <!-- Infos -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-amber-900">{{ $item['nom'] }}</h3>
                                    <p class="text-sm text-amber-600">Quantité : {{ $item['quantite'] }}</p>
                                    <p class="text-xs mt-1">
                                        <span class="text-amber-500">🕯️ {{ $item['parfum'] }}</span>
                                    </p>
                                </div>
                                
                                <!-- Prix -->
                                <div class="text-right">
                                    <p class="text-lg font-bold text-amber-900">
                                        {{ number_format($item['sous_total'], 2) }} €
                                    </p>
                                    <p class="text-xs text-amber-600">{{ number_format($item['prix_unitaire'], 2) }} € / unité</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-amber-600">Votre panier est vide</p>
                            <a href="{{ route('kiosque') }}" class="inline-block mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl">
                                Continuer mes achats
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Mode de paiement -->
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200">
                    <h2 class="text-2xl font-bold text-amber-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💳</span> Mode de paiement
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-r from-amber-100 to-orange-100 rounded-xl border border-amber-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-8 bg-white rounded flex items-center justify-center shadow-sm">
                                        <span class="text-xs font-bold text-amber-700">CB</span>
                                    </div>
                                    <div>
                                        <p class="text-amber-900 font-semibold">Carte bancaire</p>
                                        <p class="text-xs text-amber-600">Visa, Mastercard, American Express</p>
                                    </div>
                                </div>
                                <div class="text-green-600 text-sm font-semibold">✓ Sélectionné</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 text-xs text-amber-600 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Paiement crypté et sécurisé par Stripe</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Total et action -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200 sticky top-8">
                    <h2 class="text-xl font-bold text-amber-900 mb-6">Récapitulatif financier</h2>

                    <!-- Totaux -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm text-amber-700">
                            <span>Sous-total ({{ count($items) }} article{{ count($items) > 1 ? 's' : '' }})</span>
                            <span class="font-medium">{{ number_format($total, 2) }} €</span>
                        </div>
                        
                        <div class="flex justify-between text-sm text-amber-700">
                            <span>Livraison</span>
                            <span class="text-green-600 font-medium">Gratuite</span>
                        </div>
                        
                        <div class="flex justify-between text-sm text-amber-700">
                            <span>Frais de traitement</span>
                            <span class="text-green-600 font-medium">Offerts</span>
                        </div>
                        
                        <div class="border-t border-amber-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-amber-900">Total à payer</span>
                                <span class="text-2xl font-bold text-amber-700">
                                    {{ number_format($total, 2) }} €
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de paiement -->
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
                            <p class="font-semibold">⚠️ Erreur de paiement</p>
                            <p class="text-sm">{{ session('error') }}</p>
                            <p class="text-xs mt-2">Vérifiez que les clés Stripe sont configurées dans le fichier .env</p>
                        </div>
                    @endif
                    
                    <form action="{{ route('payment.checkout') }}" method="POST">
                        @csrf
                        @php
                            $orderId = (isset($order) && $order) ? $order->id : session('pending_order_id');
                        @endphp
                        <input type="hidden" name="order_id" value="{{ $orderId }}">
                        
                        @if(!$orderId)
                            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-xl">
                                <p>⚠️ Problème de création de commande. <a href="{{ route('orders.create') }}" class="underline">Recommencer</a></p>
                            </div>
                        @endif
                        <button type="submit"
                            class="w-full px-6 py-4 bg-gradient-to-r from-amber-600 to-orange-500 hover:from-amber-500 hover:to-orange-400 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg mb-4 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Payer maintenant</span>
                        </button>
                    </form>

                    <!-- Bouton retour -->
                    <a href="{{ route('orders.create') }}"
                        class="w-full px-6 py-3 bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold rounded-xl transition-colors text-center block mb-6">
                        ← Retour
                    </a>

                    <!-- Garantie -->
                    <div class="pt-6 border-t border-amber-200">
                        <div class="space-y-3 text-xs text-amber-600">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span>Achat sécurisé et crypté</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span>Satisfait ou remboursé (14 jours)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Paiement CB sécurisé</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection