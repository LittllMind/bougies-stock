<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Rapports
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Inventaire -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">🕯️ Inventaire des bougies</h3>
                            <p class="mt-1 text-sm text-gray-500">Téléchargez un rapport PDF complet de votre inventaire avec statistiques et alertes stock.</p>
                        </div>
                        <a href="{{ route('admin.reports.inventory.pdf') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            📥 Télécharger PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Financier -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">💰 Rapport financier</h3>
                            <p class="mt-1 text-sm text-gray-500">Générez un rapport des ventes sur une période donnée.</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.reports.financial.pdf') }}" class="flex items-end gap-4">
                        <div class="flex-1">
                            <label for="debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" name="debut" id="debut" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                   value="{{ now()->subMonth()->format('Y-m-d') }}">
                        </div>
                        
                        <div class="flex-1">
                            <label for="fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" name="fin" id="fin" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                        
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                📥 Télécharger PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
