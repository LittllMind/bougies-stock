<nav class="nav">
    <div class="container nav-container">
        <div class="nav-brand">
            <a href="{{ route('vinyles.index') }}">Stock Vinyles</a>
        </div>

        <div class="nav-menu">
            {{-- Liens réservés aux utilisateurs authentifiés --}}
            @auth
                <a href="{{ route('vinyles.index') }}" class="{{ request()->routeIs('vinyles.*') ? 'active' : '' }}">
                    Vinyles
                </a>
                <a href="{{ route('ventes.index') }}" class="{{ request()->routeIs('ventes.*') ? 'active' : '' }}">
                    Ventes
                </a>
                <a href="{{ route('stats') }}" class="{{ request()->routeIs('stats') ? 'active' : '' }}">
                    Statistiques
                </a>
                <a href="{{ route('kiosque.index') }}" target="_blank">
                    Kiosque
                </a>
            @endauth


        </div>

        <div class="flex items-center gap-4">
            <x-cart-badge />

            {{-- Autres éléments (notifications, profil, etc.) --}}
        </div>

        <div class="nav-user">
            @auth
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-link">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-link">Connexion</a>
                <a href="{{ route('register') }}" class="btn-link">Inscription</a>
            @endauth
        </div>
    </div>
</nav>
