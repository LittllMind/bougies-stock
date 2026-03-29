<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Les bougies de Séraphie') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>[x-cloak] { display: none !important; }</style>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'seph-gold': '#D4AF37',
                            'seph-cream': '#F5F5DC',
                            'seph-warm': '#FFF8DC',
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="bg-seph-warm text-gray-800 min-h-screen" x-data="{ mobileMenuOpen: false }">
        
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="pt-24">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 py-12 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-2 mb-4 md:mb-0">
                        <span class="text-2xl">🕯️</span>
                        <span class="text-xl font-serif text-amber-400">Les bougies de Séraphie</span>
                    </a>
                    <div class="flex space-x-6 text-gray-400">
                        <a href="{{ route('about') }}" class="hover:text-amber-400 transition">L'atelier</a>
                        <a href="{{ route('contact') }}" class="hover:text-amber-400 transition">Contact</a>
                    </div>
                </div>
                <div class="mt-8 text-center text-gray-500 text-sm">
                    © {{ date('Y') }} Les bougies de Séraphie. Tous droits réservés. 🐝 Fabriqué avec amour en France.
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
