
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vinyle Hydrodécoupé')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-gray-800/90 backdrop-blur-sm border-b border-gray-700 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    💿 Vinyle Hydrodécoupé
                </a>
                <div class="flex items-center gap-6">
                    <a href="/kiosque" class="hover:text-purple-400 transition">Catalogue</a>
                    <a href="/about" class="hover:text-purple-400 transition">Le Concept</a>
                    <a href="/contact" class="hover:text-purple-400 transition">Contact</a>
                    @auth
                        <a href="/cart" class="hover:text-purple-400 transition">Panier</a>
                        <a href="/addresses" class="hover:text-purple-400 transition" title="Mes adresses">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>
                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">Déconnexion</button>
                        </form>
                    @else
                        <a href="/login" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg transition">Connexion</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        @if (session('success'))
            <div class="alert alert-success bg-green-600 text-white px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error bg-red-600 text-white px-4 py-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center text-gray-400">
            <p>© 2026 Vinyle Hydrodécoupé - Artisanat & Passion</p>
        </div>
    </footer>

</body>
</html>

