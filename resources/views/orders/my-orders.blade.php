@extends('layouts.client')

@section('title', 'Mes Commandes')

@section('client-content')
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-bold text-amber-900 flex items-center">
            📦 Mes Commandes
        </h1>
        <p class="text-gray-600 mt-1">Consultez l'historique de vos commandes</p>
    </div>

    @if($orders->isEmpty())
        <!-- Aucune commande -->
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-8 text-center">
            <div class="text-6xl mb-4">🕯️</div>
            <h2 class="text-xl font-semibold text-amber-900 mb-2">Aucune commande pour le moment</h2>
            <p class="text-gray-600 mb-6">Vous n'avez pas encore passé de commande.</p>
            <a href="{{ route('kiosque') }}" 
               class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition font-medium">
                Découvrir nos bougies
            </a>
        </div>

    @else
        <!-- Liste des commandes -->
        <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <h3 class="text-white font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Historique de vos commandes
                </h3>
            </div>

            <div class="divide-y divide-amber-100">
                @foreach($orders as $order)
                    <div class="p-6 hover:bg-amber-50/50 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Info commande -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="text-lg font-bold text-amber-900">#{{ $order->numero_commande }}</span>
                                    <span class="text-sm text-gray-400">•</span>
                                    <span class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y') }}</span>
                                    <span class="text-sm text-gray-400">•</span>
                                    <span class="text-sm text-gray-600">{{ $order->items->count() }} article(s)</span>
                                </div>

                                @php
                                    $badgeClass = match($order->statut) {
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'paid' => 'bg-green-100 text-green-800 border-green-300',
                                        'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'shipped' => 'bg-purple-100 text-purple-800 border-purple-300',
                                        'delivered' => 'bg-green-100 text-green-800 border-green-300',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                                        default => 'bg-gray-100 text-gray-800 border-gray-300',
                                    };
                                    $badgeLabel = match($order->statut) {
                                        'pending' => '🕐 En attente',
                                        'paid' => '💳 Payée',
                                        'processing' => '🔧 En préparation',
                                        'shipped' => '📦 Expédiée',
                                        'delivered' => '✅ Livrée',
                                        'cancelled' => '❌ Annulée',
                                        default => $order->statut,
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $badgeClass }}">
                                    {{ $badgeLabel }}
                                </span>
                            </div>

                            <!-- Prix -->
                            <div class="text-right">
                                <p class="text-2xl font-bold text-amber-900">{{ number_format($order->total, 2, ',', ' ') }} €</p>
                                <p class="text-sm text-gray-600">
                                    {{ $order->items->count() }} article(s)
                                </p>
                            </div>
                        </div>

                        <!-- Articles (visible par défaut) -->
                        <div class="mt-4 pt-4 border-t border-amber-100">
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between py-2">
                                        @if($item->bougie)
                                            <div class="flex items-center">
                                                <span class="text-gray-600">{{ $item->quantite }}x</span>
                                                <span class="ml-2 text-amber-900 font-medium">{{ $item->bougie->nom }}</span>
                                            </div>
                                            <span class="text-amber-700">{{ number_format($item->total, 2, ',', ' ') }} €</span>
                                        @else
                                            <span class="text-gray-500 italic">Article non disponible</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-gray-600">
                                    Livraison: {{ $order->adresse }}<br>
                                    <span class="text-sm">{{ $order->code_postal }}, {{ $order->ville }}</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $order->telephone }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-amber-100 bg-amber-50/50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection