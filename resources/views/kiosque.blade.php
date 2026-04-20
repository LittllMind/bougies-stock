{{-- resources/views/kiosque.blade.php --}}

@extends('layouts.kiosque')

@section('title', 'Nos Bougies - Les bougies de Séraphie')

@section('content')<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Hero Kiosque -->
    <div class="text-center py-12 mb-8">
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-amber-800 mb-4">
            🕯️ Nos Bougies Artisanales
        </h1>
        <p class="text-lg text-amber-700/70 max-w-2xl mx-auto">
            Chaque pièce est façonnée à la main dans notre atelier, à partir de cire d'abeille 100% naturelle.
            Découvrez notre collection de bougies sculptées uniques.
        </p>
    </div>

    <!-- Header avec panier -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-amber-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                <span class="text-2xl">🐝</span>
            </div>
            <div>
                <p class="font-semibold text-amber-800">{{ $bougies->total() }} bougie(s) disponible(s)</p>
                <p class="text-sm text-gray-500">Cire locale • Coulées à la main • Sans parfum ajouté</p>
            </div>
        </div>
        <a href="{{ route('cart.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2 shadow-sm hover:shadow-md">
            🛒 Mon Panier
            <span id="cart-badge" class="bg-amber-800 text-white text-sm px-2 py-1 rounded-full">0</span>
        </a>
    </div>

    <!-- Filtres -->
    <form method="GET" action="{{ route('kiosque') }}" class="bg-white rounded-2xl p-6 mb-8 border border-amber-100 shadow-sm">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-amber-800 mb-2">Rechercher</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-amber-400">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, parfum..."
                        class="w-full bg-amber-50/50 border border-amber-200 rounded-xl pl-10 pr-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                    >
                </div>
            </div>
            
            <div class="min-w-[200px]">
                <label class="block text-sm font-medium text-amber-800 mb-2">Collection</label>
                <select name="collection" class="w-full bg-amber-50/50 border border-amber-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500">
                    <option value="">Toutes</option>
                    @foreach($collections as $collection)
                        <option value="{{ $collection }}" {{ request('collection') == $collection ? 'selected' : '' }}>
                            {{ $collection }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                    Filtrer
                </button>

                @if(request('search') || request('collection'))
                    <a href="{{ route('kiosque') }}" class="bg-amber-100 hover:bg-amber-200 text-amber-800 px-4 py-3 rounded-xl font-medium transition">
                        ✕
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Grille de bougies -->
    @if($bougies->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($bougies as $bougie)
                <div class="bg-white rounded-2xl overflow-hidden border border-amber-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    
                    <!-- Image -->
                    <div class="aspect-square bg-gradient-to-br from-amber-100 to-orange-50 relative overflow-hidden">
                        @if($bougie->image)
                            <img src="{{ $bougie->image_url }}" alt="{{ $bougie->nom }}" 
                                class="w-full h-full object-cover hover:scale-105 transition duration-500"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-6xl">🕯️</span>
                            </div>
                        @endif
                        
                        @if($bougie->quantite <= 0)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="bg-red-500 text-white px-4 py-2 rounded-full font-semibold">Rupture</span>
                            </div>
                        @endif

                        @if($bougie->collection)
                            <div class="absolute top-4 right-4">
                                <span class="bg-amber-500/90 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $bougie->collection }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div class="p-5">
                        <h3 class="text-lg font-serif font-bold text-gray-900 mb-1 truncate">{{ $bougie->nom }}</h3>
                        <p class="text-amber-600 text-sm mb-3">{{ $bougie->parfum }}</p>

                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span>{{ $bougie->temps_brulure ? $bougie->temps_brulure . 'h' : '—' }}</span>
                            <span class="{{ $bougie->quantite > 0 ? 'text-green-600' : 'text-red-500' }}">
                                Stock: {{ $bougie->quantite }}
                            </span>
                        </div>

                        <div class="text-2xl font-bold text-amber-700 mb-4">
                            {{ number_format($bougie->prix, 2, ',', ' ') }} €
                        </div>

                        @if($bougie->quantite > 0)
                            <div class="flex gap-2">
                                <select id="qty-{{ $bougie->id }}" class="bg-amber-50 border border-amber-200 rounded-lg px-2 py-2 w-16 text-center">
                                    @for($i = 1; $i <= min(5, $bougie->quantite); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>

                                <button type="button" 
                                        onclick="addToCart({{ json_encode($bougie) }})"
                                        class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg font-semibold transition cart-btn"
                                        data-id="{{ $bougie->id }}"
                                >
                                    Ajouter au panier
                                </button>
                            </div>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed"
                            >Indisponible</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $bougies->links() }}
        </div>

    @else
        <div class="text-center py-20">
            <div class="text-6xl mb-4">🕯️</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune bougie trouvée</h3>
            <p class="text-gray-500">Essayez une autre recherche ou revenez plus tard</p>
        </div>
    @endif

    <!-- Panier mobile flottant -->
    <a href="{{ route('cart.index') }}" id="cart-mobile" class="fixed bottom-6 left-6 right-6 sm:hidden bg-amber-600 text-white text-center py-4 rounded-2xl font-semibold shadow-lg shadow-amber-300/50"
    >
        🛒 Voir mon panier
    </a>
</div>
@endsection

@push('scripts')
<script>
    // Service cart inline pour éviter dépendance externe
    const CART_KEY = 'bougies_cart';
    
    function getCart() {
        const cart = localStorage.getItem(CART_KEY);
        if (cart) {
            return JSON.parse(cart);
        }
        return { items: [], total: 0, count: 0 };
    }
    
    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        window.dispatchEvent(new Event('cart-updated'));
    }
    
    function addToCart(bougie) {
        const qtySelect = document.getElementById('qty-' + bougie.id);
        const quantity = qtySelect ? parseInt(qtySelect.value) : 1;
        
        const cart = getCart();
        const existingItem = cart.items.find(item => item.reference === bougie.reference);
        
        if (existingItem) {
            existingItem.quantite += quantity;
            existingItem.sous_total = existingItem.quantite * existingItem.prix_unitaire;
        } else {
            cart.items.push({
                reference: bougie.reference,
                nom: bougie.nom,
                parfum: bougie.parfum,
                prix_unitaire: parseFloat(bougie.prix),
                quantite: quantity,
                sous_total: parseFloat(bougie.prix) * quantity
            });
        }
        
        cart.count = cart.items.reduce((sum, item) => sum + item.quantite, 0);
        cart.total = cart.items.reduce((sum, item) => sum + item.sous_total, 0);
        
        saveCart(cart);
        updateCartBadge();
        
        // Feedback visuel
        const btn = document.querySelector(`button[data-id="${bougie.id}"]`);
        if (btn) {
            const originalText = btn.textContent;
            btn.textContent = '✓ Ajoutée !';
            btn.classList.add('bg-green-600');
            btn.classList.remove('bg-amber-600');
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-600');
                btn.classList.add('bg-amber-600');
            }, 1500);
        }
    }
    
    // Mettre à jour le badge du panier
    function updateCartBadge() {
        const cart = getCart();
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.textContent = cart.count || 0;
        }
        
        // Mise à jour aussi du panier mobile
        const mobileCart = document.getElementById('cart-mobile');
        if (mobileCart) {
            mobileCart.textContent = `🛒 Voir mon panier (${cart.count || 0})`;
        }
    }
    
    // Mettre à jour au chargement
    updateCartBadge();
    
    // Écouter les changements de panier de d'autres onglets
    window.addEventListener('storage', (e) => {
        if (e.key === 'bougies_cart') {
            updateCartBadge();
        }
    });
</script>
@endpush
