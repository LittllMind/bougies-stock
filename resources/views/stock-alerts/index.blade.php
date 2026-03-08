{{-- resources/views/stock-alerts/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Alertes Stock - Fundisc')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            🚨 Alertes Stock
        </h1>
        <p class="text-gray-400 mt-2">Gestion des alertes et seuils de stock</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-red-900/50 to-red-800/30 border border-red-700/50 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-300 text-sm font-medium">Rupture de stock</p>
                    <p class="text-3xl font-bold text-red-400 mt-1">{{ $outOfStockItems->count() }}</p>
                </div>
                <div class="text-4xl">⛔</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-900/50 to-yellow-800/30 border border-yellow-700/50 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-300 text-sm font-medium">Stock faible</p>
                    <p class="text-3xl font-bold text-yellow-400 mt-1">{{ $lowStockItems->count() }}</p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-900/50 to-purple-800/30 border border-purple-700/50 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-300 text-sm font-medium">Alertes actives</p>
                    <p class="text-3xl font-bold text-purple-400 mt-1">{{ $alerts->where('statut', 'actif')->count() }}</p>
                </div>
                <div class="text-4xl">🔔</div>
            </div>
        </div>
    </div>

    <!-- Alerte Rupture -->
    @if($outOfStockItems->isNotEmpty())
    <div class="bg-red-900/20 border border-red-700/50 rounded-2xl p-6 mb-8">
        <h3 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
            ⛔ Ruptures de stock
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($outOfStockItems as $vinyle)
            <div class="bg-gray-800/50 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($vinyle->getFirstMediaUrl('photo'))
                        <img src="{{ $vinyle->getFirstMediaUrl('photo') }}" alt="" class="w-12 h-12 object-cover rounded-lg">
                    @else
                        <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center">💿</div>
                    @endif
                    <div>
                        <p class="font-semibold text-white">{{ $vinyle->nom }}</p>
                        <p class="text-sm text-gray-400">{{ $vinyle->modele ?? 'N/A' }}</p>
                        <p class="text-xs text-red-400 mt-1">Stock : {{ $vinyle->quantite }}</p>
                    </div>
                </div>
                <a href="{{ route('vinyles.edit', $vinyle) }}" class="text-purple-400 hover:text-purple-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Alerte Stock Faible -->
    @if($lowStockItems->isNotEmpty())
    <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-2xl p-6 mb-8">
        <h3 class="text-xl font-bold text-yellow-400 mb-4 flex items-center gap-2">
            ⚠️ Stocks faibles (≤ seuil)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lowStockItems as $vinyle)
            <div class="bg-gray-800/50 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($vinyle->getFirstMediaUrl('photo'))
                        <img src="{{ $vinyle->getFirstMediaUrl('photo') }}" alt="" class="w-12 h-12 object-cover rounded-lg">
                    @else
                        <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center">💿</div>
                    @endif
                    <div>
                        <p class="font-semibold text-white">{{ $vinyle->nom }}</p>
                        <p class="text-sm text-gray-400">{{ $vinyle->modele ?? 'N/A' }}</p>
                        <p class="text-xs text-yellow-400 mt-1">
                            Stock : {{ $vinyle->quantite }} / Seuil : {{ $vinyle->seuil_alerte ?? 1 }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('vinyles.edit', $vinyle) }}" class="text-purple-400 hover:text-purple-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tableau des Alertes -->
    <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Toutes les alertes</h3>
            <a href="{{ route('stock-alerts.history') }}" class="text-purple-400 hover:text-purple-300 text-sm">
                Voir l'historique →
            </a>
        </div>

        @if($alerts->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-4xl mb-4">✅</p>
                <p>Aucune alerte active. Tout va bien !</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Vinyle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Détails</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($alerts as $alert)
                        <tr class="hover:bg-gray-700/30 {{ $alert->statut === 'resolu' ? 'opacity-50' : '' }}">
                            <td class="px-6 py-4">
                                @if($alert->alertable)
                                    <a href="{{ route('vinyles.show', $alert->alertable) }}" class="text-purple-400 hover:text-purple-300">
                                        {{ $alert->alertable->nom }}
                                    </a>
                                @else
                                    <span class="text-gray-500">Vinyle supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($alert->quantite_actuelle <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-400 border border-red-700">
                                        Rupture
                                    </span>
                                @elseif($alert->quantite_actuelle <= $alert->seuil_alerte)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-400 border border-yellow-700">
                                        Faible
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-400">
                                        Info
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                Qté: {{ $alert->quantite_actuelle }} / Seuil: {{ $alert->seuil_alerte }}
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm">
                                {{ $alert->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($alert->statut === 'actif')
                                    <form action="{{ route('stock-alerts.resolve', $alert) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-400 hover:text-green-300 text-sm flex items-center gap-1 ml-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Résoudre
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm">Résolue</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
