@extends('layouts.client')

@section('title', 'Mon Espace')

@section('client-content')
    <!-- En-tête inline au lieu de x-slot -->
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-bold text-amber-900 flex items-center">
            🕯️ Bienvenue, {{ Auth::user()->name }}
        </h1>
        <p class="text-gray-600 mt-1">Votre espace personnel Séraphie</p>
    </div>

    <!-- Statistiques Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total commandes -->
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Commandes passées</p>
                    <p class="text-2xl font-bold text-amber-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total dépenses -->
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total dépensé</p>
                    <p class="text-2xl font-bold text-amber-900">{{ number_format($stats['total_spent'], 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>

        <!-- Bougie préférée -->
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bougie favorite</p>
                    <p class="text-lg font-bold text-amber-900 truncate">
                        {{ $favoriteBougie ? $favoriteBougie->nom : 'Aucune encore' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernière commande -->
    @if($latestOrder)
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <h3 class="text-white font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Dernière commande
                </h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Commande #{{ $latestOrder->numero_commande }}</p>
                        <p class="text-gray-600">{{ $latestOrder->created_at->format('d/m/Y') }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'shipped' => 'bg-blue-100 text-blue-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'En attente',
                            'paid' => 'Payée',
                            'shipped' => 'Expédiée',
                            'delivered' => 'Livrée',
                            'cancelled' => 'Annulée',
                        ];
                    @endphp
                    <span class="mt-2 md:mt-0 px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$latestOrder->statut] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$latestOrder->statut] ?? $latestOrder->statut }}
                    </span>
                </div>

                <div class="border-t border-amber-100 pt-4">
                    @foreach($latestOrder->items as $item)
                        <div class="flex justify-between items-center py-2">
                            <div class="flex items-center">
                                <span class="text-gray-700">{{ $item->quantite }}x {{ $item->bougie->nom }}</span>
                            </div>
                            <span class="text-amber-900 font-medium">{{ number_format($item->total, 2, ',', ' ') }} €</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center border-t border-amber-100 pt-4 mt-4">
                    <span class="text-gray-600">Total</span>
                    <span class="text-xl font-bold text-amber-900">{{ number_format($latestOrder->total, 2, ',', ' ') }} €</span>
                </div>

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('orders.my') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition">
                        Voir toutes mes commandes →
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-8 text-center mb-8">
            <div class="text-6xl mb-4">🕯️</div>
            <h3 class="text-xl font-bold text-amber-900 mb-2">Bienvenue chez Séraphie !</h3>
            <p class="text-gray-600 mb-6">Vous n'avez pas encore passé de commande.</p>
            <a href="{{ route('kiosque') }}" class="inline-flex items-center px-6 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition font-medium">
                Découvrir nos bougies
            </a>
        </div>
    @endif

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('addresses.index') }}" class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6 hover:border-amber-300 transition group">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-600 group-hover:bg-amber-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h4 class="font-bold text-amber-900">Gérer mes adresses</h4>
                    <p class="text-sm text-gray-600">Ajouter ou modifier vos adresses de livraison</p>
                </div>
            </div>
        </a>

        <a href="{{ route('profile.edit') }}" class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6 hover:border-amber-300 transition group">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 group-hover:bg-orange-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h4 class="font-bold text-amber-900">Modifier mon profil</h4>
                    <p class="text-sm text-gray-600">Changer mon email ou mon mot de passe</p>
                </div>
            </div>
        </a>
    </div>
@endsection