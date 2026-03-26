<nav class="nav">
    <div class="container nav-container">
        <div class="nav-brand">
            <a href="/">Les bougies de Séraphie</a>
        </div>

        <div class="nav-menu">
            {{-- Liens réservés aux utilisateurs authentifiés --}}
            @auth
                <a href="{{ route('admin.bougies.index') }}" class="{{ request()->routeIs('admin.bougies.*') ? 'active' : '' }}">
                    Bougies
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        Utilisateurs
                    </a>
                @endif
                <a href="{{ route('kiosque') }}" target="_blank">
                    Catalogue
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
