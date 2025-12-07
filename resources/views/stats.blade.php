<x-app-layout>
    <x-slot name="header">
        <h2>Statistiques</h2>
    </x-slot>

    <div class="page-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📀</div>
                <div class="stat-content">
                    <h3>{{ $totalVinyles }}</h3>
                    <p>Vinyles au catalogue</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3>{{ number_format($valeurStock, 2) }} €</h3>
                    <p>Valeur du stock</p>
                </div>
            </div>

            <div class="stat-card {{ $stockBas > 0 ? 'stat-warning' : '' }}">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3>{{ $stockBas }}</h3>
                    <p>Vinyles en stock bas</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <h3>{{ $totalVentes }}</h3>
                    <p>Ventes totales</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-content">
                    <h3>{{ number_format($chiffreAffaires, 2) }} €</h3>
                    <p>Chiffre d'affaires</p>
                </div>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Top 5 Modèles</h3>
                <canvas id="topModelesChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>Ventes par Mois</h3>
                <canvas id="ventesChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>Répartition des Paiements</h3>
                <canvas id="paiementsChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Top Modèles
        const topModelesCtx = document.getElementById('topModelesChart');
        new Chart(topModelesCtx, {
            type: 'bar',
            data: {
                labels: {
                    !!$topModeles - > pluck('modele') - > toJson() !!
                },
                datasets: [{
                    label: 'Nombre de vinyles',
                    data: {
                        !!$topModeles - > pluck('count') - > toJson() !!
                    },
                    backgroundColor: '#4F46E5',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Ventes par Mois
        const ventesCtx = document.getElementById('ventesChart');
        new Chart(ventesCtx, {
            type: 'line',
            data: {
                labels: {
                    !!$ventesParMois - > pluck('mois') - > toJson() !!
                },
                datasets: [{
                    label: 'Chiffre d\'affaires (€)',
                    data: {
                        !!$ventesParMois - > pluck('total') - > toJson() !!
                    },
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
            }
        });

        // Paiements
        const paiementsCtx = document.getElementById('paiementsChart');
        new Chart(paiementsCtx, {
            type: 'doughnut',
            data: {
                labels: {
                    !!$paiements - > pluck('mode_paiement') - > map(fn($m) => ucfirst($m)) - > toJson() !!
                },
                datasets: [{
                    data: {
                        !!$paiements - > pluck('count') - > toJson() !!
                    },
                    backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6'],
                }]
            },
            options: {
                responsive: true,
            }
        });
    </script>
</x-app-layout>