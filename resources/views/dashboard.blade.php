@extends('layouts.kiosque')

@section('title', 'Mon Compte - Les bougies de Séraphie')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="bg-gradient-to-br from-amber-50 to-[#F5F5DC] rounded-3xl p-8 mb-8 border border-amber-200/50">
        <div class="flex items-center gap-4 mb-4">
            <span class="text-5xl">🕯️</span>
            <div>
                <h1 class="text-3xl sm:text-4xl font-serif text-amber-800">
                    👋 Bonjour, {{ Auth::user()->name }} !
                </h1>
                <p class="text-amber-700/70 mt-1">
                    {{ Auth::user()->getRoleLabel() }} | Les bougies de Séraphie
                </p>
            </div>
        </div>
    </div>

    <!-- Section Client (Tous les rôles) -->
    <div class="mb-10">
        <h2 class="text-xl font-serif text-amber-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-px bg-amber-300"></span>
            🛍️ Espace Client
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Catalogue -->
            <a href="{{ route('kiosque') }}" 
               class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                    <span class="text-2xl">🕯️</span>
                </div>
                <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Catalogue</h3>
                <p class="text-sm text-amber-600/70 mt-1">Parcourir les bougies disponibles</p>
            </a>

            <!-- Panier -->
            <a href="{{ route('cart.index') }}" 
               class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                    <span class="text-2xl">🛒</span>
                </div>
                <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Mon Panier</h3>
                <p class="text-sm text-amber-600/70 mt-1">Voir mon panier</p>
            </a>

            <!-- Mes commandes -->
            <a href="{{ route('orders.my') }}" 
               class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                    <span class="text-2xl">📦</span>
                </div>
                <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Mes Commandes</h3>
                <p class="text-sm text-amber-600/70 mt-1">Historique de mes commandes</p>
            </a>

            <!-- Mes adresses -->
            <a href="{{ route('addresses.index') }}" 
               class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                    <span class="text-2xl">📍</span>
                </div>
                <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Mes Adresses</h3>
                <p class="text-sm text-amber-600/70 mt-1">Gérer mes adresses de livraison</p>
            </a>
        </div>
    </div>

    @auth
        @if(Auth::user()->isEmployeOrAdmin())
        <!-- Section Admin/Employé -->
        <div class="mb-10">
            <h2 class="text-xl font-serif text-amber-800 mb-6 flex items-center gap-3">
                <span class="w-8 h-px bg-amber-300"></span>
                🔧 Gestion du Stock
                <span class="text-sm font-normal text-amber-600/60">(Admin/Employé)</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Stock Bougies -->
                <a href="{{ route('admin.bougies.index') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">🕯️</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Stock Bougies</h3>
                    <p class="text-sm text-amber-600/70 mt-1">Gérer le catalogue des bougies</p>
                </a>

                <!-- Alertes Stock -->
                <a href="{{ route('admin.stock-alerts.index') }}" 
                   class="group bg-gradient-to-br from-red-50 to-amber-50 rounded-xl p-6 shadow-sm hover:shadow-md border border-red-200/30 hover:border-red-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-200 transition">
                        <span class="text-2xl">🚨</span>
                    </div>
                    <h3 class="text-lg font-semibold text-red-600 group-hover:text-red-700">Alertes Stock</h3>
                    <p class="text-sm text-red-500/70 mt-1">Suivi des ruptures et niveaux faibles</p>
                </a>

                <!-- Commandes -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">📋</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Commandes</h3>
                    <p class="text-sm text-amber-600/70 mt-1">Gérer les commandes clients</p>
                </a>

                <!-- Mode Marché -->
                <a href="{{ route('marche.index') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">🛍️</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Mode Marché</h3>
                    <p class="text-sm text-amber-600/70 mt-1">Interface vente physique</p>
                </a>

                <!-- Rapports PDF -->
                <a href="{{ route('admin.reports.index') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">📄</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Rapports PDF</h3>
                    <p class="text-sm text-amber-600/70 mt-1">Export inventaire et finances</p>
                </a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin())
        <!-- Section Admin Only -->
        <div class="mb-10">
            <h2 class="text-xl font-serif text-amber-800 mb-6 flex items-center gap-3">
                <span class="w-8 h-px bg-amber-300"></span>
                📊 Administration
                <span class="text-sm font-normal text-amber-600/60">(Admin uniquement)</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">📈</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Statistiques</h3>
                    <p class="text-sm text-amber-600/70 mt-1">CA, stocks, analyses</p>
                </a>

                <!-- Utilisateurs -->
                <a href="{{ route('admin.users.index') }}" 
                   class="group bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-amber-100/50 hover:border-amber-300 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition">
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="text-lg font-semibold text-amber-700 group-hover:text-amber-800">Utilisateurs</h3>
                    <p class="text-sm text-amber-600/70 mt-1">Gérer les comptes utilisateurs</p>
                </a>
            </div>
        </div>
        @endif
    @endauth
</div>
@endsection
