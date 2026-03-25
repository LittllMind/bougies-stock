<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bougies Artisanales')</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;600&display=swap" rel="stylesheet">
    
    {{-- Tailwind styles for artisanal candles --}}
    <style>
        :root {
            /* Palette chaleureuse */
            --cream: #FDF8F3;
            --cream-dark: #F5EDE4;
            --gold: #C9A962;
            --gold-light: #D4BC7E;
            --sage: #8FA68E;
            --terracotta: #C17A5C;
            --charcoal: #2C2C2C;
            --warm-gray: #6B6560;
            
            /* Typography */
            --font-serif: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Lato', -apple-system, sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            background-color: var(--cream);
            color: var(--charcoal);
            line-height: 1.6;
        }
        
        /* Header styles */
        .main-header {
            background: linear-gradient(to bottom, var(--cream), var(--cream-dark));
            border-bottom: 1px solid rgba(201, 169, 98, 0.2);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: var(--charcoal);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo::before {
            content: "🕯️";
            font-size: 1.5rem;
        }
        
        .logo span {
            color: var(--gold);
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--warm-gray);
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.03em;
            transition: color 0.2s ease;
            position: relative;
        }
        
        .nav-links a:hover {
            color: var(--charcoal);
        }
        
        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: var(--gold);
        }
        
        /* Cart button */
        .cart-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--gold);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: var(--font-body);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .cart-btn:hover {
            background: var(--gold-light);
        }
        
        .cart-count {
            background: white;
            color: var(--gold);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Main content */
        main {
            min-height: calc(100vh - 200px);
        }
        
        /* Footer */
        .main-footer {
            background-color: var(--cream-dark);
            border-top: 1px solid rgba(201, 169, 98, 0.2);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }
        
        .footer-col h4 {
            font-family: var(--font-serif);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--charcoal);
        }
        
        .footer-col p, .footer-col a {
            color: var(--warm-gray);
            font-size: 0.9rem;
            text-decoration: none;
            line-height: 1.8;
        }
        
        .footer-col a:hover {
            color: var(--gold);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(143, 166, 142, 0.3);
            color: var(--warm-gray);
            font-size: 0.85rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .nav-links {
                order: 3;
                width: 100%;
                justify-content: center;
                padding-top: 1rem;
                border-top: 1px solid rgba(201, 169, 98, 0.2);
            }
            
            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
        
        /* Utility classes */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            background: var(--gold);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .btn-primary:hover {
            background: var(--gold-light);
            transform: translateY(-1px);
        }
        
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            background: transparent;
            color: var(--charcoal);
            border: 1px solid var(--gold);
            border-radius: 4px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .btn-outline:hover {
            background: var(--gold);
            color: white;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="/" class="logo">
                Les Bougies<span>Artisanales</span>
            </a>
            
            <nav class="nav-links">
                <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'active' : '' }}">Accueil</a>
                <a href="{{ route('catalogue') }}" class="{{ request()->routeIs('catalogue*') ? 'active' : '' }}">Catalogue</a>
                <a href="{{ route('about') }}">À propos</a>
                <a href="{{ route('contact') }}">Contact</a>
                
                @auth
                <a href="{{ route('orders.my') }}">Mes commandes</a>
                @endauth
            </nav>
            
            <a href="{{ route('cart.index') }}" class="cart-btn">
                <span>🛒</span>
                <span>Panier</span>
                @php
                    $cartService = app(\App\Services\CartService::class);
                    $cartCount = $cartService->getCart()->items->sum('quantite');
                @endphp
                @if($cartCount > 0)
                    <span class="cart-count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
    
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-col">
                <h4>🕯️ Les Bougies Artisanales</h4>
                <p>Bougies sculptées faites à la main en France. Chaque pièce est unique et créée sur mesure.</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <p><a href="{{ route('catalogue') }}">Catalogue</a></p>
                <p><a href="{{ route('about') }}">À propos</a></p>
                <p><a href="{{ route('contact') }}">Contact</a></p>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>📧 contact@bougies-artisanales.fr</p>
                <p>📍 Fabriqué en France</p>
            </div>
        </div>
        <div class="footer-bottom">
            © {{ date('Y') }} Les Bougies Artisanales — Fabriqué avec passion en France
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
