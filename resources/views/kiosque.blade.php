{{-- resources/views/kiosque.blade.php - Version simple sans Alpine.js --}}

@php
    use App\Services\CartService;
    $cartService = app(CartService::class);
    $cart = $cartService->getCart();
    $cartCount = $cart->items->sum('quantite');
@endphp

@extends('layouts.kiosque')

@section('title', 'Catalogue - Les bougies de Séraphie')

@section('content')
    <style>
        .card:hover { transform: translateY(-4px); }
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-bg:hover { background-color: #B8960C; }
    </style>

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: bold; color: #D4AF37; margin: 0;">
                🕯️ Nos Bougies Artisanales
            </h1>
            <p style="color: #9ca3af; margin: 4px 0 0;">Cire d'abeille 100% naturelle, façonnée à la main</p>
        </div>
        <a href="{{ route('cart.index') }}" style="background: #D4AF37; color: #1a1a1a; padding: 12px 24px; border-radius: 16px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            🛒 Mon Panier <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 12px; font-size: 0.875rem;">{{ $cartCount }}</span>
        </a>
    </div>

    <!-- Filtres simples -->
    <form method="GET" action="{{ route('kiosque') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
            style="background: #1f2937; border: 1px solid #374151; color: #fff; padding: 12px 16px; border-radius: 16px; min-width: 250px;"
        >
        
        <select name="collection" style="background: #1f2937; border: 1px solid #374151; color: #fff; padding: 12px; border-radius: 12px;">
            <option value="">Toutes les collections</option>
            @foreach($collections as $collection)
                <option value="{{ $collection }}" {{ request('collection') == $collection ? 'selected' : '' }}>
                    {{ $collection }}
                </option>
            @endforeach
        </select>

        <button type="submit" style="background: #D4AF37; color: #1a1a1a; border: none; padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: 600;">
            Filtrer
        </button>

        @if(request('search') || request('collection'))
            <a href="{{ route('kiosque') }}" style="color: #9ca3af; text-decoration: underline;">Réinitialiser</a>
        @endif
    </form>

    <!-- Grille de bougies -->
    @if($bougies->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
            @foreach($bougies as $bougie)
                <div class="card" style="background: #1f2937; border-radius: 16px; overflow: hidden; border: 1px solid #374151; transition: all 0.3s;">
                    <!-- Image -->
                    <div style="height: 224px; background: #111827; position: relative; overflow: hidden;">
                        <img src="{{ $bougie->image_url ?? '/images/candles/no-image.png' }}" alt="{{ $bougie->nom }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.src='/images/candles/no-image.png'"
                        >
                        
                        @if($bougie->quantite <= 0)
                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center;">
                                <span style="background: #dc2626; color: white; padding: 8px 16px; border-radius: 12px; font-weight: 600;">Rupture</span>
                            </div>
                        @endif

                        @if($bougie->collection)
                            <div style="position: absolute; top: 8px; right: 8px;">
                                <span style="background: rgba(212, 175, 55, 0.9); color: #1a1a1a; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $bougie->collection }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div style="padding: 16px;">
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: #f3f4f6; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $bougie->nom }}</h3>
                        <p style="color: #D4AF37; font-size: 0.875rem; margin: 0 0 8px;">{{ $bougie->parfum }}</p>

                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #9ca3af; margin-bottom: 12px;">
                            <span>{{ $bougie->temps_brulure ? $bougie->temps_brulure . 'h' : '—' }}</span>
                            <span>Stock: {{ $bougie->quantite }}</span>
                        </div>

                        <div style="font-size: 1.5rem; font-weight: bold; color: #D4AF37; margin-bottom: 12px;">
                            {{ number_format($bougie->prix, 2, ',', ' ') }} €
                        </div>

                        @if($bougie->quantite > 0)
                            <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 8px;">
                                @csrf
                                <input type="hidden" name="bougie_id" value="{{ $bougie->id }}">
                                
                                <select name="quantite" style="background: #374151; color: #fff; border: 1px solid #4b5563; border-radius: 8px; padding: 8px; width: 60px;">
                                    @for($i = 1; $i <= min(5, $bougie->quantite); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>

                                <button type="submit" style="flex: 1; background: #D4AF37; color: #1a1a1a; border: none; padding: 10px; border-radius: 12px; cursor: pointer; font-weight: 600;">
                                    Ajouter
                                </button>
                            </form>
                        @else
                            <button disabled style="width: 100%; background: #374151; color: #9ca3af; border: none; padding: 10px; border-radius: 12px; cursor: not-allowed;">
                                Indisponible
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top: 32px;">
            {{ $bougies->links() }}
        </div>

    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 3.75rem; margin-bottom: 16px;">🕯️</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #9ca3af;">Aucune bougie trouvée</h3>
            <p style="color: #6b7280; margin-top: 8px;">Essayez une autre recherche ou revenez plus tard</p>
        </div>
    @endif

    <!-- Panier mobile flottant -->
    <div style="position: fixed; bottom: 16px; left: 16px; right: 16px; z-index: 50; display: none;">
        <a href="{{ route('cart.index') }}" style="display: block; background: #D4AF37; color: #1a1a1a; text-align: center; padding: 16px; border-radius: 16px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            🛒 Voir mon panier ({{ $cartCount }})
        </a>
    </div>

@endsection
