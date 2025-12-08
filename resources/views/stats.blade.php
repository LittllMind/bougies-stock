<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <h2>Statistiques</h2>

            {{-- Filtre de période --}}
            <form method="GET" action="{{ route('stats') }}" style="display: flex; gap: 0.5rem; align-items: center;">
                <label for="periode">Période :</label>
                <select name="periode" id="periode" onchange="this.form.submit()">
                    <option value="30j" {{ $periode === '30j' ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="3m" {{ $periode === '3m' ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="12m" {{ $periode === '12m' ? 'selected' : '' }}>12 derniers mois</option>
                    <option value="all" {{ $periode === 'all' ? 'selected' : '' }}>Depuis le début</option>
                </select>
            </form>
        </div>
    </x-slot>

    <div class="page-content">
        {{-- CARTES STATS (8 cartes) --}}
        <div class="stats-grid">
            {{-- 1 --}}


            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h2>{{ number_format($valeurStock, 2, ',', ' ') }} €</h2>
                    <p>Valeur du stock (catalogue)</p>
                </div>
            </div>

            {{-- 2 --}}

            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-content">
                    <h2>{{ number_format($chiffreAffaires, 2, ',', ' ') }} €</h2>
                    <p>Chiffre d'affaires {{ $periodeLabel }}</p>
                </div>
            </div>

            {{-- 3 --}}

            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <h2>{{ $totalVentes }}</h2>
                    <p>Ventes sur {{ $periodeLabel }}</p>
                </div>
            </div>

            {{-- 4 --}}

            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <h2>{{ number_format($caMoyenParJour, 2, ',', ' ') }} €</h2>
                    <p>CA moyen par jour ({{ $periodeLabel }})</p>
                </div>
            </div>

            {{-- 5 --}}

            <div class="stat-card">
                <div class="stat-icon">📀</div>
                <div class="stat-content">
                    <h2>{{ $totalVinyles }}</h2>
                    <p>Modeles au catalogue</p>
                </div>
            </div>

            {{-- 6  --}}

            <div class="stat-card">
                <div class="stat-icon">💿</div>
                <div class="stat-content">
                    <h2>{{ $nbVinylesVendus }}</h2>
                    <p>Vinyles vendus sur {{ $periodeLabel }}</p>
                </div>
            </div>


            {{-- 7 : Panier moyen --}}

            <a href="{{ route('vinyles.index', ['filter' => 'stock_bas']) }}" class="stat-card-link">
                <div class="stat-card {{ $stockBas > 0 ? 'stat-warning' : '' }}">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-content">
                        <h2>{{ $stockBas }}</h2>
                        <p>Vinyles en stock bas (1 à 3)</p>
                    </div>
                </div>
            </a>

            {{-- 8 : vinyles vendus --}}
            <a href="{{ route('vinyles.index', ['filter' => 'rupture']) }}" class="stat-card-link">
                <div class="stat-card {{ $rupturesStock > 0 ? 'stat-warning' : '' }}">
                    <div class="stat-icon">🛑</div>
                    <div class="stat-content">
                        <h2>{{ $rupturesStock }}</h2>
                        <p>Ruptures de stock</p>
                    </div>
                </div>
            </a>

        </div>

        {{-- GRAPHIQUES --}}
        <div class="charts-container">
            <div class="chart-card">
                <h2>Top 10 modèles vendus ({{ $periodeLabel }})</h2>
                <canvas id="topModelesChart"></canvas>
            </div>

            <div class="chart-card">
                <h2>Ventes ({{ $periodeLabel }}) – CA / {{ $grouping === 'day' ? 'jour' : 'mois' }}</h2>
                <canvas id="ventesChart"></canvas>
            </div>

            <div class="chart-card">
                <h2>Répartition des paiements ({{ $periodeLabel }})</h2>
                <canvas id="paiementsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ============================
        // Données injectées depuis PHP
        // ============================
        const topModelesLabels = {!! $topModelesVendus->pluck('nom')->toJson() !!};
        const topModelesData = {!! $topModelesVendus->pluck('total_vendus')->toJson() !!};

        const ventesLabels = {!! $ventesParPeriode->pluck('periode')->toJson() !!};
        const ventesData = {!! $ventesParPeriode->pluck('total')->toJson() !!};

        const paiementsModes = {!! $paiements->pluck('mode_paiement')->toJson() !!};
        const paiementsCounts = {!! $paiements->pluck('count')->toJson() !!};
        const paiementsTotals = {!! $paiements->pluck('total')->toJson() !!};

        const paiementsLabels = paiementsModes.map(label => {
            if (!label) return '-';
            return label.charAt(0).toUpperCase() + label.slice(1);
        });

        // ============================
        // Graphique Top 10 modèles vendus
        // ============================
        const topModelesCtx = document.getElementById('topModelesChart').getContext('2d');
        new Chart(topModelesCtx, {
            type: 'bar',
            data: {
                labels: topModelesLabels,
                datasets: [{
                    label: 'Quantité vendue',
                    data: topModelesData,
                    backgroundColor: '#4F46E5',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // ============================
        // Graphique Ventes – CA par jour / mois
        // ============================
        const ventesCtx = document.getElementById('ventesChart').getContext('2d');
        new Chart(ventesCtx, {
            type: 'line',
            data: {
                labels: ventesLabels,
                datasets: [{
                    label: "Chiffre d'affaires (€)",
                    data: ventesData,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // ============================
        // Graphique Répartition Paiements
        // ============================
        const paiementsCtx = document.getElementById('paiementsChart').getContext('2d');
        new Chart(paiementsCtx, {
            type: 'doughnut',
            data: {
                labels: paiementsLabels,
                datasets: [{
                    data: paiementsCounts, // nombre de ventes
                    backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981', '#6366F1'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const ventes = paiementsCounts[index] ?? 0;
                                const total = paiementsTotals[index] ?? 0;
                                return `${ventes} ventes – ${total.toFixed(2)} €`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
