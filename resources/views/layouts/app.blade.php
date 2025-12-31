<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="header">
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="container">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
     </div>

    {{-- Dans ton layout principal --}}
    @php
        /** @var \App\Services\CartService $cartService */
        $cartService = app(\App\Services\CartService::class);
        $cart = $cartService->getCart();
        // Si ton modèle Cart a bien une méthode totalItems()
        $cartCount = method_exists($cart, 'totalItems')
            ? $cart->totalItems()
            : $cart->items->sum('quantite'); // fallback si besoin
    @endphp

    <div class="flex items-center gap-4">
        <a href="{{ route('cart.index') }}" class="relative">
            🛒 Panier
            @if ($cartCount > 0)
                <span
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            @endif
        </a>
    </div>
</body>
</html>
