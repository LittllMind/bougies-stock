@extends('layouts.app')

@section('title', 'Historique des Mouvements de Stock')

@section('content')
<div class="p-6">
    <!-- Header avec stats -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-[#1A365D]">📦 Historique des Mouvements</h1>
                <p class="text-gray-500 mt-1">Suivi des entrées et sorties de stock</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('mouvements.index', request()->all()) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#1A365D] text-white rounded-lg hover:bg-[#2C5282] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Exporter CSV
                </a>
            </div>
        </div>

        <!-- Stats rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Entrées</p>
                        <p class="text-2xl font-bold text-green-600">+{{ $stats['total_entrees'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Sorties</p>
                        <p class="text-2xl font-bold text-red-600">-{{ $stats['total_sorties'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Mouvements Aujourd'hui</p>
                        <p class="text-2xl font-bold text-[#1A365D]">{{ $stats['aujourdhui'] }}</p>
                    </div>
                    <div class="p-3 bg-[#1A365D] rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('mouvements.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-[#1A365D] focus:ring-[#1A365D]">
                    <option value="">Tous</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Produit Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                <select name="produit_type" class="w-full rounded-lg border-gray-300 focus:border-[#1A365D] focus:ring-[#1A365D]">
                    <option value="">Tous</option>
                    @foreach($produitTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('produit_type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date début -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-[#1A365D] focus:ring-[#1A365D]">
            </div>

            <!-- Date fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-[#1A365D] focus:ring-[#1A365D]">
            </div>

            <!-- Recherche -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Référence..."
                           class="w-full rounded-lg border-gray-300 focus:border-[#1A365D] focus:ring-[#1A365D]">
                </div>
            </div>

            <!-- Boutons -->
            <div class="md:col-span-5 flex gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1A365D] text-white rounded-lg hover:bg-[#2C5282] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('mouvements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Par</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mouvements as $mouvement)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[#1A365D]">
                                {{ $mouvement->date_mouvement->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $mouvement->date_mouvement->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $mouvement->type_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $mouvement->produit_libelle }}</span>
                            <div class="text-xs text-gray-500">ID: {{ $mouvement->produit_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold {{ $mouvement->type === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $mouvement->type === 'entree' ? '+' : '-' }}{{ $mouvement->quantite }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-[#1A365D] flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($mouvement->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-900">{{ $mouvement->user?->name ?? 'Système' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 font-mono">{{ $mouvement->reference ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $mouvement->notes ?? '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-500 text-lg">Aucun mouvement trouvé</p>
                                <p class="text-gray-400 text-sm mt-1">Les mouvements apparaîtront ici lors des entrées/sorties de stock</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $mouvements->links() }}
    </div>
</div>
@endsection