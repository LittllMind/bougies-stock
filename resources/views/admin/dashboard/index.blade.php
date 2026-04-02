@extends('layouts.admin')

@section('title', 'Dashboard - Les Bougies de Séraphie')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#333333]">Dashboard</h1>
        <p class="text-gray-600 mt-2">Vue d'ensemble du stock de bougies</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Nombre total de bougies -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total bougies</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalBougies }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Valeur stock total -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#228B22]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Valeur stock</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($valeurStockTotal, 2, ',', ' ') }} €</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-[#228B22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alertes actives -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Alertes actives</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $alertesActives }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.stock-alerts.index') }}" class="text-sm text-red-500 hover:text-red-700 mt-2 inline-block">Voir les alertes →</a>
        </div>

        <!-- En stock -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">En stock</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $bougiesEnStock }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers mouvements de stock -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
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
