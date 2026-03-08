@extends('layouts.kiosque')

@section('title', 'Gestion des Fonds - Vinyle Hydrodécoupé')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ editing: null, showSuccess: false }">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                🎨 Gestion des Fonds
            </h1>
            <p class="text-gray-400 mt-2">Gérez les stocks de fonds spéciaux (miroirs, doré...)</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-purple-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour au Dashboard
        </a>
    </div>

    <!-- Alertes -->
    @if (session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tableau -->
    <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-lg font-semibold text-gray-300">Stocks de fonds spéciaux</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Type</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Visuel</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Quantité</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Prix d'achat</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Valeur</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @php $totalValue = 0; @endphp
                    @forelse ($fonds as $fond)
                        @php 
                            $valeurStock = $fond->quantite * $fond->prix_achat;
                            $totalValue += $valeurStock;
                            
                            $icon = match($fond->type) {
                                'standard' => '🪞',
                                'miroir' => '✨',
                                'dore' => '🌟',
                                default => '🎨',
                            };
                            $gradient = match($fond->type) {
                                'standard' => 'from-gray-600 to-gray-500',
                                'miroir' => 'from-blue-500 to-cyan-400',
                                'dore' => 'from-yellow-500 to-orange-400',
                                default => 'from-purple-500 to-pink-500',
                            };
                        @endphp

                        <tr class="hover:bg-gray-800/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">{{ $icon }}</span>
                                    <span class="font-medium text-gray-300 capitalize">{{ $fond->nom }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-2xl shadow-lg">
                                    {{ $icon }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('fonds.update', $fond) }}" 
                                      class="flex items-center gap-3"
                                      x-data="{ editing: false, qty: {{ $fond->quantite }} }">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="relative">
                                        <input type="number" 
                                               name="quantite" 
                                               min="0" 
                                               x-model="qty"
                                               @focus="editing = true"
                                               @blur="editing = false"
                                               class="w-24 px-3 py-2 bg-gray-900 border border-gray-600 rounded-xl text-center font-semibold transition focus:border-purple-500 focus:outline-none"
                                               value="{{ old('quantite', $fond->quantite) }}">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500">unités</span>
                                    </div>

                                    <button type="submit" 
                                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl transition flex items-center gap-2"
                                            :class="{ 'opacity-75 cursor-wait': !editing }"
                                            @click="editing = false">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Enregistrer
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4 text-gray-400">
                                {{ number_format($fond->prix_achat, 2, ',', ' ') }} €
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-semibold text-purple-400">{{ number_format($valeurStock, 2, ',', ' ') }} €</span>
                            </td>

                            <td class="px-6 py-4">
                                @if($fond->quantite === 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-400">
                                        Rupture
                                    </span>
                                @elseif($fond->quantite <= 3)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/20 text-yellow-400">
                                        🔶 Stock bas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400">
                                        ✅ OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-6xl mb-3">🎨</div>
                                <p class="text-gray-400">Aucun fond configuré pour le moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                
                @if(count($fonds) > 0)
                <tfoot class="bg-gray-900/50 border-t border-gray-700">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-300">Valeur totale du stock :</td>
                        <td colspan="2" class="px-6 py-4">
                            <span class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                                {{ number_format($totalValue, 2, ',', ' ') }} €
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Info -->
    <div class="mt-6 flex items-start gap-3 text-sm text-gray-500 bg-gray-800/30 p-4 rounded-xl">
        <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p>💡 <span class="font-medium text-gray-400">Astuce :</span> Cliquez sur la quantité pour la modifier, puis appuyez sur le bouton "Enregistrer" pour sauvegarder. Les alertes s'affichent automatiquement selon le niveau de stock.</p>
    </div>
</div>
@endsection