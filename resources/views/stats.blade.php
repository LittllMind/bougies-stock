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

        {{-- ===================== --}}
        {{-- 1. CARTES PRINCIPALES --}}
        {{-- ===================== --}}
        <h3 class="section-title">Vue d'ensemble</h3>

        <div class="stats-grid">
            {{-- 1 : Investissement total vinyles --}}
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h2>{{ number_format($investissementTotalVinyles, 2, ',', ' ') }} €</h2>
                    <p>Investissement total vinyles (stock + vendus)</p>
                </div>
            </div>

            {{-- 2 : Vinyles total (stock + vendus) --}}
            <div class="stat-card">
                <div class="stat-icon">💿</div>
                <div class="stat-content">
                    <h2>{{ $quantiteVinylesAchetes }}</h2>
                    <p>Vinyles totaux (stock + vendus)</p>
                </div>
            </div>

            {{-- 3 : Fonds total (stock + vendus) --}}
            <div class="stat-card">
                <div class="stat-icon">🪞</div>
                <div class="stat-content">
                    <h2>{{ $quantiteFondsAchetesTotal }}</h2>
                    <p>Fonds totaux (stock + vendus)</p>
                </div>
            </div>

            {{-- 4 : CA total possible (historique + stock) --}}
            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-content">
                    <h2>{{ number_format($caTotalPossibleVinyles, 2, ',', ' ') }} €</h2>
                    <p>CA total possible (réalisé + stock vinyles)</p>
                </div>
            </div>

            {{-- 5 : Coût d'achat vinyles vendus (historique) --}}
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h2>{{ number_format($coutAchatVinylesVendus, 2, ',', ' ') }} €</h2>
                    <p>Coût d'achat vinyles vendus (historique)</p>
                </div>
            </div>

            {{-- 6 : Vinyles vendus (historique) --}}
            <div class="stat-card">
                <div class="stat-icon">💿</div>
                <div class="stat-content">
                    <h2>{{ $quantiteVinylesVendus }}</h2>
                    <p>Vinyles vendus (historique)</p>
                </div>
            </div>

            {{-- 7 : Fonds vendus (historique) --}}
            <div class="stat-card">
                <div class="stat-icon">🪞</div>
                <div class="stat-content">
                    <h2>{{ $quantiteFondsVendusTotal }}</h2>
                    <p>Fonds consommés / vendus (historique)</p>
                </div>
            </div>

            {{-- 8 : CA total réalisé --}}
            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-content">
                    <h2>{{ number_format($chiffreAffairesTotal, 2, ',', ' ') }} €</h2>
                    <p>CA total réalisé (historique)</p>
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- 2. DÉTAIL VINYLES & FONDS --}}
        {{-- ========================= --}}
        <h3 class="section-title">Détail vinyles & fonds</h3>

        <div class="stats-grid">
            {{-- 9 : Valeur stock vinyles (achat) --}}
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <h2>{{ number_format($valeurStockAchatVinyles, 2, ',', ' ') }} €</h2>
                    <p>Valeur d'achat du stock vinyles</p>
                </div>
            </div>

            {{-- 10 : Valeur stock vinyles (prix vente) --}}
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-content">
                    <h2>{{ number_format($valeurStock, 2, ',', ' ') }} €</h2>
                    <p>Valeur du stock vinyles au prix catalogue</p>
                </div>
            </div>

            {{-- 11 : Vinyles en stock --}}
            <div class="stat-card">
                <div class="stat-icon">💿</div>
                <div class="stat-content">
                    <h2>{{ $quantiteVinylesStock }}</h2>
                    <p>Vinyles en stock</p>
                </div>
            </div>

            {{-- 12 : Stock bas / ruptures --}}
            <a href="{{ route('vinyles.index', ['filter' => 'stock_bas']) }}" class="stat-card stat-card-clickable">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3>{{ $stockBas }}</h3>
                    <p>Stock bas (≤ 3)</p>
                </div>
            </a>

            {{-- 13 Ruptures de stock --}}
            <a href="{{ route('vinyles.index', ['filter' => 'rupture']) }}"
                class="stat-card stat-card-clickable stat-card-danger">
                <div class="stat-icon">🚨</div>
                <div class="stat-content">
                    <h3>{{ $rupturesStock }}</h3>
                    <p>Ruptures de stock</p>
                </div>
            </a>

            {{-- 14 : Détail fonds en stock --}}
            <div class="stat-card">
                <div class="stat-icon">🪞</div>
                <div class="stat-content">
                    <h2>{{ $quantiteFondsMiroirStock }} / {{ $quantiteFondsDoreStock }}</h2>
                    <p>Fonds miroir / doré en stock</p>
                </div>
            </div>

            {{-- 15 : Valeur d'achat stock fonds --}}
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h2>{{ number_format($valeurStockFonds, 2, ',', ' ') }} €</h2>
                    <p>Valeur d'achat du stock fonds</p>
                </div>
            </div>

            {{-- 16 : Investissement total fonds --}}
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h2>{{ number_format($investissementTotalFonds, 2, ',', ' ') }} €</h2>
                    <p>Investissement total fonds (stock + vendus)</p>
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- 3. STATS SUR LA PÉRIODE  --}}
        {{-- ========================= --}}
        <h3 class="section-title">
            Ventes sur la période – {{ $periodeLabel }}
        </h3>

        <div class="stats-grid">
            {{-- 17 : Nombre de ventes --}}
            <div class="stat-card">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <h2>{{ $totalVentes }}</h2>
                    <p>Nombre de ventes</p>
                </div>
            </div>

            {{-- 18 : CA sur la période --}}
            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-content">
                    <h2>{{ number_format($chiffreAffaires, 2, ',', ' ') }} €</h2>
                    <p>Chiffre d'affaires sur la période</p>
                </div>
            </div>

            {{-- 19 : Panier moyen --}}
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <h2>{{ number_format($panierMoyen, 2, ',', ' ') }} €</h2>
                    <p>Panier moyen</p>
                </div>
            </div>

            {{-- 20 : Marge brute sur la période --}}
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <h2>{{ number_format($margeBrute, 2, ',', ' ') }} €</h2>
                    <p>Marge brute (période)</p>
                </div>
            </div>

            {{-- 21 : Vinyles vendus sur la période --}}
            <div class="stat-card">
                <div class="stat-icon">💿</div>
                <div class="stat-content">
                    <h2>{{ $nbVinylesVendus }}</h2>
                    <p>Vinyles vendus sur la période</p>
                </div>
            </div>

            {{-- 22 : Marge globale historique --}}
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <h2>{{ number_format($margeBruteTotale, 2, ',', ' ') }} €</h2>
                    <p>Marge brute totale historique</p>
                </div>
            </div>

            {{-- 23 : Taux de marge historique --}}
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <h2>{{ number_format($tauxMargeBruteTotale, 1, ',', ' ') }} %</h2>
                    <p>Taux de marge brute historique</p>
                </div>
            </div>

            {{-- 24 : Marge potentielle sur le stock vinyles --}}
            <div class="stat-card">
                <div class="stat-icon">🚀</div>
                <div class="stat-content">
                    <h2>{{ number_format($margePotentielleStock, 2, ',', ' ') }} €</h2>
                    <p>Marge potentielle sur le stock vinyles</p>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- 4. GRAPHIQUES        --}}
        {{-- ===================== --}}
        <h3 class="section-title">Graphiques</h3>

        <div class="charts-grid">
            {{-- Top modèles vendus --}}
            <div class="chart-card">
                <h4>Top modèles vendus ({{ $periodeLabel }})</h4>
                <canvas id="topModelesChart"></canvas>
            </div>

            {{-- Ventes par période --}}
            <div class="chart-card">
                <h4>Ventes par {{ $grouping === 'day' ? 'jour' : 'mois' }}</h4>
                <canvas id="ventesChart"></canvas>
            </div>

            {{-- Répartition des paiements --}}
            <div class="chart-card">
                <h4>Répartition des modes de paiement</h4>
                <canvas id="paiementsChart"></canvas>
            </div>
        </div>
    </div>







    {{-- =========================
     HISTORIQUE VINYLES
========================= --}}
    <h2 class="text-xl font-semibold mt-8 mb-4">Vinyles – Historique global</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Quantités --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Vinyles en stock (quantité)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteVinylesStock, 0, ',', ' ') }}
            </p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Vinyles vendus (historique)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteVinylesVendus, 0, ',', ' ') }}
            </p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Vinyles achetés (stock + vendus)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteVinylesAchetes, 0, ',', ' ') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        {{-- Coût d'achat des vinyles en stock --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Coût d'achat des vinyles en stock</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($valeurStockAchatVinyles, 2, ',', ' ') }} €
            </p>
        </div>

        {{-- Coût d'achat des vinyles vendus (historique) --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Coût d'achat des vinyles vendus (historique)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($coutAchatVinylesVendus, 2, ',', ' ') }} €
            </p>
        </div>

        {{-- Investissement total vinyles --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Investissement total vinyles (achetés)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($investissementTotalVinyles, 2, ',', ' ') }} €
            </p>
        </div>
    </div>


    {{-- =========================
     HISTORIQUE FONDS
========================= --}}
    <h2 class="text-xl font-semibold mt-8 mb-4">Fonds spéciaux – Historique global</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Quantités en stock --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Fonds en stock (total)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteFondsStockTotal, 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Miroir : {{ $quantiteFondsMiroirStock }} • Doré : {{ $quantiteFondsDoreStock }}
            </p>
        </div>

        {{-- Quantités vendues --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Fonds vendus (historique)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteFondsVendusTotal, 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Miroir : {{ $quantiteFondsMiroirVendus }} • Doré : {{ $quantiteFondsDoreVendus }}
            </p>
        </div>

        {{-- Quantités achetées --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Fonds achetés (stock + vendus)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($quantiteFondsAchetesTotal, 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Miroir : {{ $quantiteFondsMiroirAchetes }} • Doré : {{ $quantiteFondsDoreAchetes }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        {{-- Coût d'achat des fonds en stock --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Coût d'achat des fonds en stock</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($valeurStockFonds, 2, ',', ' ') }} €
            </p>
        </div>

        {{-- Coût d'achat des fonds vendus (historique) --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Coût d'achat des fonds vendus (historique)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($coutAchatFondsVendus, 2, ',', ' ') }} €
            </p>
        </div>

        {{-- Investissement total fonds --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-sm font-medium text-gray-500">Investissement total fonds (achetés)</h3>
            <p class="text-2xl font-bold mt-2">
                {{ number_format($investissementTotalFonds, 2, ',', ' ') }} €
            </p>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- 5. SCRIPTS CHART.JS  --}}
    {{-- ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données PHP -> JS
        const topModelesLabels = {!! $topModelesVendus->pluck('nom')->toJson() !!};
        const topModelesData = {!! $topModelesVendus->pluck('total_vendus')->toJson() !!};

        const ventesLabels = {!! $ventesParPeriode->pluck('periode')->toJson() !!};
        const ventesData = {!! $ventesParPeriode->pluck('ca')->toJson() !!};

        const paiementsModes = {!! $paiements->pluck('mode_paiement')->toJson() !!};
        const paiementsCounts = {!! $paiements->pluck('nb_ventes')->toJson() !!};
        const paiementsTotals = {!! $paiements->pluck('total')->toJson() !!};

        // ============================
        // Graphique Top modèles vendus
        // ============================
        const topModelesCtx = document.getElementById('topModelesChart').getContext('2d');
        new Chart(topModelesCtx, {
            type: 'bar',
            data: {
                labels: topModelesLabels,
                datasets: [{
                    label: 'Vinyles vendus',
                    data: topModelesData,
                    backgroundColor: '#3B82F6',
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                scales: {
                    x: {
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
                },
                scales: {
                    y: {
                        ticks: {
                            precision: 0
                        }
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
                labels: paiementsModes,
                datasets: [{
                    data: paiementsCounts,
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
