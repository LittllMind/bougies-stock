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
                <a href="{{ route('landing') }}" class="text-gray-600 hover:text-amber-700 transition {{ request()->routeIs('landing') ? 'text-amber-700 font-semibold' : '' }}">Accueil</a>
                <a href="{{ route('kiosque') }}" class="text-gray-600 hover:text-amber-700 transition {{ request()->routeIs('kiosque') || request()->routeIs('catalogue*') ? 'text-amber-700 font-semibold' : '' }}">Nos Bougies</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-amber-700 transition {{ request()->routeIs('about') ? 'text-amber-700 font-semibold' : '' }}">L'Atelier</a>
                <a href="{{ route('contact') }}" class="text-gray-600 hover:text-amber-700 transition {{ request()->routeIs('contact') ? 'text-amber-700 font-semibold' : '' }}">Contact</a>
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
            <a href="{{ route('landing') }}" @click="mobileMenuOpen = false" class="block {{ request()->routeIs('landing') ? 'text-amber-700 font-semibold' : 'text-gray-600 hover:text-amber-700' }} py-2 pt-4">Accueil</a>
            <a href="{{ route('kiosque') }}" @click="mobileMenuOpen = false" class="block {{ request()->routeIs('kiosque') ? 'text-amber-700 font-semibold' : 'text-gray-600 hover:text-amber-700' }} py-2">Nos Bougies</a>
            <a href="{{ route('about') }}" @click="mobileMenuOpen = false" class="block {{ request()->routeIs('about') ? 'text-amber-700 font-semibold' : 'text-gray-600 hover:text-amber-700' }} py-2">L'Atelier</a>
            <a href="{{ route('contact') }}" @click="mobileMenuOpen = false" class="block {{ request()->routeIs('contact') ? 'text-amber-700 font-semibold' : 'text-gray-600 hover:text-amber-700' }} py-2">Contact</a>
            
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