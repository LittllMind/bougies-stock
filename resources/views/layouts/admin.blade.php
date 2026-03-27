<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Les Bougies de Séraphie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

    <!-- Navigation Admin -->
    <nav class="bg-[#D4AF37] text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="/admin/dashboard" class="text-xl font-bold flex items-center gap-2">
                    🕯️ Les Bougies de Séraphie
                </a>
                <div class="flex items-center gap-6">
                    <a href="/admin/dashboard" class="hover:text-yellow-100 transition">Dashboard</a>
                    <a href="/admin/bougies" class="hover:text-yellow-100 transition">Bougies</a>
                    <a href="/admin/orders" class="hover:text-yellow-100 transition">Commandes</a>
                    <a href="/kiosque" class="hover:text-yellow-100 transition">Kiosque</a>
                    <a href="/" class="bg-white/20 px-3 py-1 rounded hover:bg-white/30 transition">Site</a>
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-yellow-100 transition">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="bg-green-500 text-white px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500 text-white px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
