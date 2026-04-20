@extends('layouts.admin')

@section('title', 'Dashboard - Les Bougies de Séraphie')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#333333]">Dashboard</h1>
        <p class="text-gray-600 mt-2">Vue d'ensemble de votre activité</p>
    </div>

    <!-- CARDS STATS (T5.1) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Ventes du jour -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Ventes du jour</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ventesDuJour, 2, ',', ' ') }} €</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock faible -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Stock faible</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stockFaible }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.bougies.index') }}" class="text-sm text-red-500 hover:text-red-700 mt-2 inline-block">Gérer le stock →</a>
        </div>

        <!-- Commandes en attente -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#228B22]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Commandes en attente</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $commandesEnAttente }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-[#228B22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:text-green-800 mt-2 inline-block">Voir toutes les commandes →</a>
        </div>
    </div>

    <!-- DEUXIÈME LIGNE: Graphique + Alertes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Graphique ventes 30 jours (simple CSS/HTML) -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#333333]">Ventes sur 30 jours</h2>
            </div>
            <div id="ventesChart" class="h-72 flex items-end justify-between gap-1 px-2">
                @forelse($donneesVentes30Jours as $jour)
                    @php
                        $maxVente = $donneesVentes30Jours->max('montant') ?: 1;
                        $hauteur = $maxVente > 0 ? ($jour['montant'] / $maxVente) * 100 : 0;
                        $hauteur = max($hauteur, 2); // Minimum 2% pour visibilité
                    @endphp
                    <div class="flex flex-col items-center flex-1 group relative">
                        <div class="text-xs text-gray-600 mb-1 opacity-0 group-hover:opacity-100 transition-opacity absolute -top-6 whitespace-nowrap bg-gray-800 text-white px-2 py-1 rounded">
                            {{ number_format($jour['montant'], 0, ',', ' ') }} €
                        </div>
                        <div class="w-full bg-[#D4AF37] rounded-t transition-all duration-300 hover:bg-[#B8960B]" style="height: {{ $hauteur }}%"></div>
                        @if($loop->iteration % 5 == 0 || $loop->first || $loop->last)
                            <span class="text-xs text-gray-500 mt-1">{{ $jour['date'] }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">·</span>
                        @endif
                    </div>
                @empty
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        Aucune donnée de vente
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Alertes stock critique -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#333333]">Alertes stock critique</h2>
                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $produitsStockCritique->count() }} produits</span>
            </div>
            
            @if($produitsStockCritique->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                    <svg class="w-12 h-12 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Aucun produit en stock critique</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($produitsStockCritique as $produit)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ $produit->quantite }}
                                </div>
                                <div>
                                    <p class="font-medium text-[#333333]">{{ $produit->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $produit->reference ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.bougies.edit', $produit) }}" class="text-sm text-red-600 hover:text-red-800">
                                Modifier →
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.bougies.index') }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm font-medium">Gérer le stock →</a>
                </div>
            @endif
        </div>
    </div>

    <!-- TROISIÈME LIGNE: Dernières commandes -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-[#333333]">Dernières commandes</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm font-medium">Voir toutes les commandes →</a>
        </div>
        
        @if($dernieresCommandes->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p>Aucune commande récente</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Référence</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Client</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-600">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Statut</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dernieresCommandes as $order)
                            <tr class="border-t hover:bg-[#F5F5DC] transition">
                                <td class="px-4 py-3 text-sm font-medium text-[#333333]">
                                    #{{ $order->numero_commande ?? substr($order->id, -6) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $order->user?->name ?? 'Client anonyme' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                                    {{ number_format($order->total, 2, ',', ' ') }} €
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $order->created_at?->diffForHumans() ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    @switch($order->statut)
                                        @case('pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                            @break
                                        @case('paid')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Payée</span>
                                            @break
                                        @case('processing')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">En préparation</span>
                                            @break
                                        @case('ready')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Prête</span>
                                            @break
                                        @default
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $order->statut }}</span>
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm font-medium">Voir les détails →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ANCIENNE SECTION: Stats bougies (conservée mais condensée) -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-[#D4AF37]">
            <p class="text-xs font-medium text-gray-500 uppercase">Total bougies</p>
            <p class="text-xl font-bold text-gray-800">{{ $totalBougies }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-[#228B22]">
            <p class="text-xs font-medium text-gray-500 uppercase">Valeur stock</p>
            <p class="text-xl font-bold text-gray-800">{{ number_format($valeurStockTotal, 0, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-xs font-medium text-gray-500 uppercase">Alertes actives</p>
            <p class="text-xl font-bold text-gray-800">{{ $alertesActives }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-[#D4AF37]">
            <p class="text-xs font-medium text-gray-500 uppercase">En stock</p>
            <p class="text-xl font-bold text-gray-800">{{ $bougiesEnStock }}</p>
        </div>
    </div>

    <!-- Derniers mouvements de stock (conservé) -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-[#333333]">Derniers mouvements (entrées/sorties)</h2>
            <a href="{{ route('mouvements.index') }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm font-medium">Voir tout →</a>
        </div>
        
        @if($derniersMouvements->isEmpty())
            <p class="text-gray-500 text-center py-4">Aucun mouvement récent</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Type</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Produit</th>
                            <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">Quantité</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($derniersMouvements as $mouvement)
                            <tr class="border-t hover:bg-[#F5F5DC] transition">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $mouvement->date_mouvement?->format('d/m/Y H:i') ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($mouvement->type === 'entree')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Entrée</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sortie</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $mouvement->produit_type }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right font-medium">{{ $mouvement->quantite }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $mouvement->reference ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
