<nav class="nav">
    <div class="container nav-container">
        <div class="nav-brand">
            <a href="{{ route('vinyles.index') }}">Stock Vinyles</a>
        </div>

        <div class="nav-menu">
            <a href="{{ route('vinyles.index') }}" class="{{ request()->routeIs('vinyles.*') ? 'active' : '' }}">
                Vinyles
            </a>
            <a href="{{ route('ventes.index') }}" class="{{ request()->routeIs('ventes.*') ? 'active' : '' }}">
                Ventes
            </a>
            <a href="{{ route('stats') }}" class="{{ request()->routeIs('stats') ? 'active' : '' }}">
                Statistiques
            </a>
            <a href="{{ route('kiosque') }}" target="_blank">
                Kiosque
            </a>
        </div>

        <div class="nav-user">
            <span>{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-link">Déconnexion</button>
            </form>
        </div>
    </div>
</nav>