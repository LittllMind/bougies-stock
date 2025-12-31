{{-- resources/views/cart/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🛒 Mon Panier
            </h2>
            <a href="{{ url('/kiosque') }}" class="text-blue-600 hover:text-blue-800">
                ← Continuer mes achats
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Messages de succès/erreur --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Alertes stock --}}
            @if (!empty($stockErrors))
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                    <p class="font-semibold mb-2">⚠️ Problèmes de stock :</p>
                    <ul class="list-disc list-inside">
                        @foreach ($stockErrors as $error)
                            <li>{{ $error['message'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($cart->isEmpty())
                {{-- Panier vide --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-6xl mb-4">🛒</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">
                            Votre panier est vide
                        </h3>
                        <p class="text-gray-500 mb-6">
                            Découvrez notre sélection de vinyles vintage
                        </p>
                        <a href="{{ url('/kiosque') }}"
                            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                            Voir les vinyles
                        </a>
                    </div>
                </div>
            @else
                {{-- Panier avec articles --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Colonne principale : Liste des articles --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">
                                    Articles ({{ $cart->totalItems() }})
                                </h3>

                                <div class="space-y-4">
                                    @foreach ($cart->items as $item)
                                        <div class="flex justify-between py-2 border-b">
                                            <div>
                                                <div class="font-semibold">
                                                    {{ $item->vinyle->nom }}
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    Fond :
                                                    @if ($item->fond)
                                                        {{ ucfirst($item->fond->type) }}
                                                    @else
                                                        Standard
                                                    @endif
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    Quantité : {{ $item->quantite }}
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div>{{ number_format($item->prix_unitaire, 2, ',', ' ') }} € / u</div>
                                                <div class="font-bold">
                                                    {{ number_format($item->prix_unitaire * $item->quantite, 2, ',', ' ') }}
                                                    €
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Vider le panier --}}
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <form method="POST" action="{{ route('cart.clear') }}"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir vider le panier ?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            🗑️ Vider le panier
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne latérale : Récapitulatif --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-4">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Récapitulatif</h3>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Articles</span>
                                        <span class="font-medium">{{ $cart->totalItems() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Sous-total</span>
                                        <span class="font-medium">{{ number_format($cart->total(), 2) }} €</span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-blue-600">{{ number_format($cart->total(), 2) }} €</span>
                                    </div>
                                </div>

                                @if (empty($stockErrors))
                                    <button type="button"
                                        class="block w-full bg-gray-400 text-white text-center py-3 rounded-lg cursor-not-allowed"
                                        disabled>
                                        Valider ma commande (bientôt)
                                    </button>
                                @else
                                    <button disabled
                                        class="block w-full bg-gray-400 text-white text-center py-3 rounded-lg cursor-not-allowed font-semibold">
                                        Stock insuffisant
                                    </button>
                                @endif

                                @if ($cart->expires_at)
                                    <p class="text-xs text-gray-500 text-center mt-4">
                                        Votre panier expire dans {{ $cart->expires_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
