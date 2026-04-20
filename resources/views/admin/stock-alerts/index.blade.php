@extends('layouts.admin')

@section('title', 'Alertes de Stock')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#333333]">
            🚨 Alertes de Stock
        </h1>
        <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
            {{ $alertesEnAttente }} en attente
        </span>
    </div>

    @if(session('success'))
        <div class="bg-green-500 text-white px-4 py-3 rounded mb-4 flex justify-between items-center">
            {{ session('success') }}
            <button onclick="this.parentElement.remove()" class="text-white hover:text-green-100">✕</button>
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button type="button" onclick="showTab('en-attente')"
                        id="tab-en-attente"
                        class="w-1/3 py-4 px-1 text-center border-b-2 border-[#D4AF37] text-[#D4AF37] font-medium text-sm">
                    🚨 En attente ({{ $alertesEnAttente }})
                </button>
                <button type="button" onclick="showTab('resolues')"
                        id="tab-resolues"
                        class="w-1/3 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                    ✅ Résolues ({{ $alertesResolues }})
                </button>
                <button type="button" onclick="showTab('toutes')"
                        id="tab-toutes"
                        class="w-1/3 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                    📋 Toutes ({{ $alertes->total() }})
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Tab: En attente -->
            <div id="content-en-attente" class="tab-content">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($alertes->filter(fn($a) => !$a->resolue) as $alerte)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        CRITIQUE
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $alerte->bougie?->nom ?? 'Produit supprimé' }}</div>
                                    <div class="text-sm text-gray-500">{{ $alerte->bougie?->reference ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-red-600 font-bold">{{ $alerte->stock_actuel }}</span>
                                    <span class="text-sm text-gray-500">/ {{ $alerte->seuil_alerte }} min</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $alerte->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($alerte->bougie)
                                    <a href="{{ route('admin.bougies.edit', $alerte->bougie) }}" class="text-[#D4AF37] hover:text-yellow-700 mr-3">Réapprovisionner</a>
                                    @endif
                                    <form action="{{ route('admin.stock-alerts.resolve', $alerte) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900">Marquer résolue</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Résolues -->
            <div id="content-resolues" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Résolue le</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($alertes->filter(fn($a) => $a->resolue) as $alerte)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        RÉSOLUE
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $alerte->bougie?->nom ?? 'Produit supprimé' }}</div>
                                    <div class="text-sm text-gray-500">{{ $alerte->bougie?->reference ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $alerte->stock_actuel }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $alerte->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($alerte->bougie)
                                    <a href="{{ route('admin.bougies.edit', $alerte->bougie) }}" class="text-gray-600 hover:text-gray-900">Voir</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Toutes -->
            <div id="content-toutes" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($alertes as $alerte)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($alerte->resolue)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">RÉSOLUE</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">CRITIQUE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $alerte->bougie?->nom ?? 'Produit supprimé' }}</div>
                                    <div class="text-sm text-gray-500">{{ $alerte->bougie?->reference ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm {{ $alerte->resolue ? 'text-gray-900' : 'text-red-600 font-bold' }}">{{ $alerte->stock_actuel }}</span>
                                    <span class="text-sm text-gray-500">/ {{ $alerte->seuil_alerte }} min</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $alerte->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($alerte->bougie && !$alerte->resolue)
                                    <a href="{{ route('admin.bougies.edit', $alerte->bougie) }}" class="text-[#D4AF37] hover:text-yellow-700 mr-3">Réapprovisionner</a>
                                    @endif
                                    @if(!$alerte->resolue)
                                    <form action="{{ route('admin.stock-alerts.resolve', $alerte) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900">Résolue</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center">
        {{ $alertes->links() }}
    </div>
</div>

<script>
function showTab(tabName) {
    // Cacher tout le contenu
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Réinitialiser tous les onglets
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-[#D4AF37]', 'text-[#D4AF37]');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Activer l'onglet cliqué
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-[#D4AF37]', 'text-[#D4AF37]');
}
</script>
@endsection
