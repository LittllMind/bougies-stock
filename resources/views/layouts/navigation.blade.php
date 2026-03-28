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
            
            <!-- Desktop Auth & Admin -->
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
                    
                    {{-- Menu Admin pour admin/employé --}}
                    @if(Auth::user()->hasAnyRole(['admin', 'employe']))
                        <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="flex items-center space-x-1 text-sm font-medium text-amber-700 hover:text-amber-800 transition">
                                <span>👑 Admin</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-amber-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-amber-100">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Tableau de bord</span>
                                </div>
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    📊 Tableau de bord
                                </a>
                                <a href="{{ route('admin.stock-alerts.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    🚨 Alertes stock
                                </a>
                                <div class="px-4 py-2 border-b border-amber-100 mt-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Gestion</span>
                                </div>
                                <a href="{{ route('admin.bougies.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    🕯️ Gestion des bougies
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    📦 Gestion des commandes
                                </a>
                                <div class="px-4 py-2 border-b border-amber-100 mt-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Rapports & Outils</span>
                                </div>
                                <a href="{{ route('marche.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    🛍️ Mode Marché
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    📄 Rapports PDF
                                </a>
                                @if(Auth::user()->hasRole('admin'))
                                    <div class="px-4 py-2 border-b border-amber-100 mt-2">
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Administration</span>
                                    </div>
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                        👥 Gestion utilisateurs
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    
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
                    
                    {{-- Section Admin Mobile --}}
                    @if(Auth::user()->hasAnyRole(['admin', 'employe']))
                        <div class="border-t border-amber-100 pt-4 mt-4">
                            <span class="block text-xs font-semibold text-amber-600 uppercase mb-2">👑 Administration</span>
                            <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">📊 Tableau de bord</a>
                            <a href="{{ route('admin.stock-alerts.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">🚨 Alertes stock</a>
                            <a href="{{ route('admin.bougies.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">🕯️ Gestion bougies</a>
                            <a href="{{ route('admin.orders.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">📦 Commandes</a>
                            <a href="{{ route('marche.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">🛍️ Mode Marché</a>
                            <a href="{{ route('admin.reports.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">📄 Rapports PDF</a>
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('admin.users.index') }}" @click="mobileMenuOpen = false" class="block text-gray-600 hover:text-amber-700 py-1">👥 Utilisateurs</a>
                            @endif
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-amber-100 mt-4">
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