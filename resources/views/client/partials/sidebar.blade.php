@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="w-64 min-h-screen bg-white border-r-2 border-amber-200 hidden md:block">
    <!-- Logo -->
    <div class="p-6 border-b border-amber-100">
        <a href="{{ route('landing') }}" class="flex items-center space-x-2">
            <span class="text-2xl">🕯️</span>
            <span class="font-serif text-lg font-bold text-amber-900">Séraphie</span>
        </a>
        <p class="text-xs text-gray-500 mt-1">Mon Espace Client</p>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('client.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ str_contains($currentRoute, 'client.dashboard') ? 'bg-amber-100 text-amber-900 border-r-4 border-amber-500' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-900' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Vue d'ensemble</span>
        </a>

        <!-- Mes commandes -->
        <a href="{{ route('orders.my') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ str_contains($currentRoute, 'orders') && !str_contains($currentRoute, 'admin') ? 'bg-amber-100 text-amber-900 border-r-4 border-amber-500' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-900' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="font-medium">Mes Commandes</span>
            @if(isset($pendingOrders) && $pendingOrders > 0)
                <span class="ml-auto bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOrders }}</span>
            @endif
        </a>

        <!-- Mes adresses -->
        <a href="{{ route('addresses.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ str_contains($currentRoute, 'addresses') && !str_contains($currentRoute, 'admin') && !str_contains($currentRoute, 'orders') ? 'bg-amber-100 text-amber-900 border-r-4 border-amber-500' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-900' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Mes Adresses</span>
        </a>

        <!-- Mon profil -->
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ $currentRoute === 'profile.edit' ? 'bg-amber-100 text-amber-900 border-r-4 border-amber-500' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-900' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">Mon Profil</span>
        </a>

        <hr class="my-4 border-amber-100">

        <!-- Retour boutique -->
        <a href="{{ route('kiosque') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Retour au Kiosque</span>
        </a>
    </nav>

    <!-- Version Mobile Toggle -->
    <div class="md:hidden p-4 border-t border-amber-100">
        <button onclick="document.querySelector('.sidebar-mobile').classList.toggle('hidden')" 
                class="w-full flex items-center justify-center px-4 py-2 bg-amber-500 text-white rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            Menu
        </button>
    </div>
</div>

<!-- Mobile Sidebar -->
<div class="sidebar-mobile hidden md:hidden bg-white border-r-2 border-amber-200">
    <nav class="p-4 space-y-1 border-t border-amber-100">
        <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-amber-50">
            <span class="mr-3">📊</span> Vue d'ensemble
        </a>
        <a href="{{ route('orders.my') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-amber-50">
            <span class="mr-3">📦</span> Mes Commandes
        </a>
        <a href="{{ route('addresses.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-amber-50">
            <span class="mr-3">📍</span> Mes Adresses
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-amber-50">
            <span class="mr-3">👤</span> Mon Profil
        </a>
        <hr class="my-2">
        <a href="{{ route('kiosque') }}" class="flex items-center px-4 py-3 rounded-lg text-amber-600">
            <span class="mr-3">🛒</span> Retour au Kiosque
        </a>
    </nav>
</div>