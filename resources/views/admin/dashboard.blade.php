@extends('layouts.admin')

@section('title', 'Dashboard - Les Bougies de Séraphie')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#333333]">Dashboard</h1>
        <p class="text-gray-600 mt-2">Vue d'ensemble de votre activité</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Ventes aujourd'hui -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Ventes aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ventesAujourdhui, 2, ',', ' ') }} €</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Commandes aujourd'hui -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#228B22]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Commandes aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $commandesAujourdhui }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-[#228B22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ventes ce mois -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Ventes ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ventesMois, 2, ',', ' ') }} €</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Nouveaux clients -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-[#D4AF37]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Nouveaux clients (30j)</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $nouveauxClients }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-full">
                    <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Graphique ventes -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#333333]">Bilan des ventes</h2>
                <form method="GET" class="flex gap-2">
                    <select name="periode" class="border rounded px-3 py-1 text-sm" onchange="this.form.submit()">
                        <option value="semaine" {{ $periode === 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="mois" {{ $periode === 'mois' ? 'selected' : '' }}>Ce mois</option>
                        <option value="annee" {{ $periode === 'annee' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </form>
            </div>
            <div class="h-72">
                <canvas id="ventesChart"></canvas>
            </div>
        </div>

        <!-- Alertes stock -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-[#333333] mb-4">Alertes stock</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 flex items-center justify-center bg-[#D4AF37] rounded-full text-white text-sm font-bold">{{ $alertesStock }}</span>
                        <span class="text-sm text-gray-700">Stock faible</span>
                    </div>
                    <a href="{{ route('admin.stock-alerts.index') }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm">Voir →</a>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 flex items-center justify-center bg-red-500 rounded-full text-white text-sm font-bold">{{ $rupturesStock }}</span>
                        <span class="text-sm text-gray-700">Ruptures</span>
                    </div>
                    <a href="{{ route('admin.bougies.index') }}" class="text-red-500 hover:text-red-700 text-sm">Voir →</a>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 flex items-center justify-center bg-[#228B22] rounded-full text-white text-sm font-bold text-xs">{{ number_format($valeurStock / 1000, 1, ',', ' ') }}k</span>
                        <span class="text-sm text-gray-700">€ valeur stock</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Troisième ligne -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Produits les plus vendus -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-[#333333] mb-4">Top produits</h2>
            @if($produitsTop->isEmpty())
                <p class="text-gray-500 text-center py-4">Aucune vente ce mois</p>
            @else
                <div class="space-y-3">
                    @foreach($produitsTop as $item)
                        <div class="flex items-center justify-between p-3 hover:bg-[#F5F5DC] rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 flex items-center justify-center bg-[#D4AF37] rounded-full text-white text-xs font-bold">{{ $loop->iteration }}</span>
                                <div>
                                    <p class="font-medium text-[#333333]">{{ $item->bougie?->nom ?? 'Produit supprimé' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->bougie?->reference ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-[#D4AF37]">{{ $item->total_vendu }} vendus</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-[#333333] mb-4">Commandes récentes</h2>
            @if($commandesRecentes->isEmpty())
                <p class="text-gray-500 text-center py-4">Aucune commande récente</p>
            @else
                <div class="space-y-3">
                    @foreach($commandesRecentes as $order)
                        <div class="flex items-center justify-between p-3 hover:bg-[#F5F5DC] rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#D4AF37] flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-[#333333]">#{{ $order->reference ?? substr($order->id, -6) }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->user?->name ?? 'Client anonyme' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-[#333333]">{{ number_format($order->total, 2, ',', ' ') }} €</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at?->diffForHumans() ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.orders.index') }}" class="text-[#D4AF37] hover:text-yellow-700 text-sm font-medium">Voir toutes les commandes →</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données du graphique
    const ventesData = @json($donneesPeriode);
    
    const ctx = document.getElementById('ventesChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ventesData.labels,
                datasets: [{
                    label: 'Ventes (€)',
                    data: ventesData.ventes,
                    backgroundColor: '#D4AF37',
                    borderColor: '#B8960B',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
@endpush
