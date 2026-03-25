<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les bougies de Séraphie — Cire d'abeille 100% naturelle</title>
    <meta name="description" content="Bougies artisanales en cire d'abeille 100% naturelle, coulées à la main par Séraphie. Sans parfum ajouté, sans substance toxique.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
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

    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md fixed w-full z-50 border-b border-amber-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                    <span class="text-2xl">🕯️</span>
                    <span class="text-xl sm:text-2xl font-serif text-amber-700 tracking-tight">
                        Les bougies de Séraphie
                    </span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-amber-700 transition">Accueil</a>
                    <a href="{{ route('kiosque.index') }}" class="text-gray-600 hover:text-amber-700 transition">Nos Bougies</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-amber-700 transition">L'Atelier</a>
                    <a href="{{ route('contact') }}" class="text-gray-600 hover:text-amber-700 transition">Contact</a>
                </div>
                
                <!-- Desktop Auth -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-amber-700 transition">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-amber-600 hover:bg-amber-700 px-4 py-2 rounded-lg text-sm font-medium text-white transition">
                            Commander
                        </a>
                    @else
                        <a href="{{ route('cart.index') }}" class="text-amber-600 hover:text-amber-700 transition relative">
                            🛒 Panier
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-amber-700 transition">
                            {{ Auth::user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-400 hover:text-amber-700 transition">Déconnexion</button>
                        </form>
                    @endguest
                </div>
                
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600 p-2">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-cloak x-transition class="md:hidden mt-4 pb-4 space-y-3 border-t border-amber-100">
                <a href="{{ route('landing') }}" @click="mobileMenuOpen = false" class="block text-amber-700 font-medium py-2 pt-4">Accueil</a>
                <a href="{{ route('kiosque.index') }}" @click="mobileMenuOpen = false" class="block text-amber-600 font-semibold py-2">Nos Bougies</a>
                <a href="{{ route('about') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-2">L'Atelier</a>
                <a href="{{ route('contact') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-2">Contact</a>
                
                @auth
                    <div class="border-t border-amber-100 pt-4 mt-4 space-y-3">
                        <a href="{{ route('cart.index') }}" @click="mobileMenuOpen = false" class="block text-amber-600 font-medium py-2">🛒 Mon Panier</a>
                        <a href="{{ route('orders.my') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-2">📦 Mes commandes</a>
                        <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="block text-amber-700 font-semibold py-2">👤 Mon compte</a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-2">
                            @csrf
                            <button type="submit" class="text-amber-700 py-2">Déconnexion</button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-amber-100 pt-4 mt-4 flex flex-col gap-3">
                        <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block text-center text-gray-600 hover:text-amber-700 py-2 border border-amber-200 rounded-lg">Connexion</a>
                        <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block text-center bg-amber-600 hover:bg-amber-700 py-2 rounded-lg font-medium text-white">
                            Commander
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50"></div>
        <div class="absolute inset-0 opacity-40">
            <div class="absolute top-20 left-10 w-64 h-64 bg-amber-200 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-200 rounded-full blur-3xl" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <div class="mb-8">
                <span class="inline-block px-4 py-2 bg-amber-100/50 rounded-full text-amber-800 text-sm font-medium mb-6 border border-amber-200">
                    ✨ Artisanat Français
                </span>
            </div>
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-serif font-bold mb-4 sm:mb-6 leading-tight text-gray-900">
                <span class="text-amber-700">
                    La lumière naturelle
                </span>
                <br>
                <span class="text-gray-800">des abeilles</span>
            </h1>
            <p class="text-base sm:text-xl md:text-2xl text-gray-600 mb-8 sm:mb-10 max-w-3xl mx-auto leading-relaxed px-2 sm:px-0">
                Bougies coulées à la main en <strong>cire d'abeille 100% naturelle</strong>. 
                Sans parfum ajouté, sans substance toxique. Juste la douce odeur de miel 
                et la chaleur authentique d'une flamme vivante.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                <a href="{{ route('kiosque.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition transform hover:scale-105 shadow-lg shadow-amber-200 text-center">
                    Découvrir nos bougies
                </a>
                <a href="{{ route('about') }}" class="bg-white hover:bg-gray-50 text-gray-800 px-8 py-4 rounded-xl text-lg font-semibold transition border border-gray-200 text-center">
                    L'histoire de Séraphie
                </a>
            </div>

            <!-- Trust badges -->
            <div class="mt-12 sm:mt-16 flex flex-wrap justify-center gap-6 sm:gap-12 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <span>🐝</span>
                    <span>Cire locale</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>🌿</span>
                    <span>Sans toxine</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>✋</span>
                    <span>Fait main</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>🇫🇷</span>
                    <span>Fabriqué en France</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Bougies -->
    @if($featured->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-serif font-bold mb-4 text-gray-900">Nos Créations</h2>
                <p class="text-gray-600 text-lg">Chaque bougie est unique, façonnée à la main</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featured as $bougie)
                <div class="bg-amber-50 rounded-2xl overflow-hidden border border-amber-100 hover:border-amber-300 transition group">
                    <div class="aspect-square bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center relative overflow-hidden">
                        @if($bougie->image)
                            <img src="{{ $bougie->image_url }}" alt="{{ $bougie->nom }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <span class="text-6xl">🕯️</span>
                        @endif
                        <div class="absolute top-4 right-4 px-3 py-1 {{ $bougie->quantite > 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-full text-xs font-medium text-white">
                            {{ $bougie->quantite > 0 ? 'En Stock' : 'Épuisé' }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-serif font-semibold mb-2 text-gray-900">{{ $bougie->nom }}</h3>
                        <p class="text-amber-700 text-sm mb-4">{{ $bougie->collection }}</p>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $bougie->notes }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-amber-700">{{ number_format($bougie->prix, 2) }}€</span>
                            <a href="{{ route('kiosque.index') }}" class="text-amber-600 hover:text-amber-800 transition">
                                Voir →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('kiosque.index') }}" class="inline-block bg-amber-100 hover:bg-amber-200 text-amber-800 px-8 py-3 rounded-xl font-semibold transition">
                    Voir toute la collection
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Essence Section -->
    <section class="py-20 bg-gradient-to-br from-amber-100/50 to-orange-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-serif font-bold mb-4 text-gray-900">Pourquoi la cire d'abeille ?</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Une tradition ancestrale, un choix exigeant pour votre santé et votre bien-être
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber-200/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl">🌬️</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Air purifié</h3>
                    <p class="text-gray-600">Contrairement aux paraffines, la cire d'abeille ne dégage pas de suie toxique. Une combustion propre qui purifie l'air.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber-200/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl">⏱️</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Durée exceptionnelle</h3>
                    <p class="text-gray-600">La cire d'abeille brûle plus longtemps que les autres cires. Jusqu'à 3 fois plus longtemps que la paraffine.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber-200/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl">🍯</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Parfum subtil</h3>
                    <p class="text-gray-600">Pas de parfum synthétique. Juste l'odeur naturelle et douce du miel, apaisante et réconfortante.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Artisan Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="mb-8">
                <span class="text-6xl">👩‍🎨</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold mb-6 text-gray-900">L'Atelier de Séraphie</h2>
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                Chaque bougie est coulée à la main dans mon atelier, à quelques kilomètres des ruchers qui fournissent notre cire. 
                Je travaille chaque pièce avec soin, de la préparation du moule à la finition finale.
            </p>
            <p class="text-lg text-amber-700 font-medium mb-10">
                "Une bougie n'est pas juste de la cire et une mèche. C'est une lumière, une ambiance, un moment."
            </p>
            <a href="{{ route('about') }}" class="inline-block border-2 border-amber-600 text-amber-700 px-10 py-4 rounded-xl text-lg font-semibold hover:bg-amber-600 hover:text-white transition">
                Rencontrer l'artisane
            </a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-amber-600">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl sm:text-4xl font-serif font-bold mb-6 text-white">Prêt à illuminer votre intérieur ?</h2>
            <p class="text-xl text-amber-100 mb-10">
                Découvrez mes créations et choisissez la bougie qui vous ressemble.
            </p>
            <a href="{{ route('kiosque.index') }}" class="inline-block bg-white text-amber-700 px-10 py-4 rounded-xl text-lg font-semibold hover:bg-amber-50 transition transform hover:scale-105">
                Voir la boutique
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12">
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

</body>
</html>
