<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Bougies Stock'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&;display=swap" rel="stylesheet">

    <!-- Styles (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom styles pour catalogue */
        .bg-amber-100 { background-color: #fef3c7; }
        .bg-amber-200 { background-color: #fde68a; }
        .bg-amber-50 { background-color: #fffbeb; }
        .text-amber-400 { color: #fbbf24; }
        .text-amber-500 { color: #f59e0b; }
        .text-amber-600 { color: #d97706; }
        .text-amber-700 { color: #b45309; }
        .text-amber-800 { color: #92400e; }
        .bg-amber-500 { background-color: #f59e0b; }
        .bg-amber-600 { background-color: #d97706; }
        .hover\:bg-amber-100:hover { background-color: #fef3c7; }
        .hover\:bg-amber-600:hover { background-color: #d97706; }
        .hover\:text-amber-600:hover { color: #d97706; }
        .hover\:text-amber-700:hover { color: #b45309; }
        .border-amber-200 { border-color: #fde68a; }
        .border-amber-500 { border-color: #f59e0b; }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50">

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Flash Messages -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4"
        role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500">© {{ date('Y') }} Bougies Stock. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Vue.js from CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    @stack('scripts')
</body>

</html>
