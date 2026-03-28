<extends('admin.layout')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif text-[#D4AF37] mb-2">📊 Rapports</h1>
            <p class="text-[#666666]">Export PDF des données du système</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Rapport Inventaire --}}
            <div class="bg-white rounded-lg shadow-md p-6 border border-[#F5F5DC] hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-[#D4AF37] bg-opacity-10 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-16L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-serif text-[#333333]">Inventaire</h2>
                </div>
                
                <p class="text-[#666666] mb-6">Liste complète des bougies avec stock, valeurs et alertes.</p>
                
                <a href="{{ route('admin.reports.inventory.pdf') }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-[#D4AF37] text-white rounded hover:bg-[#C4A036] transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Télécharger PDF
                </a>
            </div>

            {{-- Rapport Financier --}}
            <div class="bg-white rounded-lg shadow-md p-6 border border-[#F5F5DC] hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-[#228B22] bg-opacity-10 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-[#228B22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-serif text-[#333333]">Financier</h2>
                </div>
                
                <p class="text-[#666666] mb-6">Revenus, commandes payées et statistiques de vente.</p>
                
                <a href="{{ route('admin.reports.financial.pdf') }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-[#228B22] text-white rounded hover:bg-[#1d7a1d] transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Télécharger PDF
                </a>
            </div>

            {{-- Rapport Alertes --}}
            <div class="bg-white rounded-lg shadow-md p-6 border border-[#F5F5DC] hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-serif text-[#333333]">Alertes Stock</h2>
                </div>
                
                <p class="text-[#666666] mb-6">Produits sous le seuil d'alerte nécessitant réapprovisionnement.</p>
                
                <a href="{{ route('admin.reports.alerts.pdf') }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Télécharger PDF
                </a>
            </div>
        </div>

        {{-- Note --}}
        <div class="mt-8 bg-[#F5F5DC] rounded-lg p-6">
            <h3 class="font-serif text-[#333333] mb-2">📋 À propos des rapports</h3>
            <ul class="text-[#666666] list-disc list-inside space-y-1">
                <li>Les rapports sont générés au format HTML optimisé pour l'impression PDF</li>
                <li>Utilisez la fonction "Imprimer en PDF" de votre navigateur pour sauvegarder</li>
                <li>Les données sont en temps réel à la génération du rapport</li>
            </ul>
        </div>

    </div>
</div>
@endsection