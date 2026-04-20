<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') ?? 'Les bougies de Séraphie' }}{{ isset($header) ? ' — ' . $header : '' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-800 antialiased bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-4 text-center">
                <a href="/" class="flex items-center justify-center space-x-2 text-2xl font-serif text-amber-700">
                    <span class="text-3xl">🕯️</span>
                    <span>Les bougies de Séraphie</span>
                </a>
                
                {{-- Sous-titre contextuel --}}
                @isset($header)
                    <p class="mt-2 text-lg text-amber-600 font-medium">{{ $header }}</p>
                @else
                    <p class="mt-2 text-sm text-gray-500">L'art de la lumière pure ✨</p>
                @endisset
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-amber-100">
                {{ $slot }}
            </div>
            
            <p class="mt-6 text-sm text-gray-500">
                🐝 Cire d'abeille 100% naturelle • Sans parfum ajouté
            </p>
        </div>
    </body>
</html>