<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-xl font-bold text-white">
            📍 Mes Adresses
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-amber-50">
        
        <!-- Sidebar Navigation Client -->
        @php
            // Pass pendingOrders count to sidebar
            $pendingOrders = \App\Models\Order::where('user_id', Auth::id())
                ->where('statut', 'pending')
                ->count();
        @endphp
        @include('client.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                
                <!-- En-tête -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <p class="text-gray-600 mt-1">Gérez vos adresses de livraison</p>
                    </div>
                    <a href="{{ route('addresses.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nouvelle adresse
                    </a>
                </div>

                <!-- Liste des adresses -->
                @if($addresses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($addresses as $address)
                            <div class="bg-white rounded-xl shadow-md border-2 border-amber-100 p-6 {{ $address->is_default ? 'ring-2 ring-amber-500' : '' }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">🏠</span>
                                        <h3 class="text-lg font-bold text-amber-900">
                                            {{ $address->label }}
                                            @if($address->is_default)
                                                <span class="ml-2 px-2 py-0.5 text-xs bg-amber-500 text-white rounded-full">Par défaut</span>
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('addresses.edit', $address->id) }}"
                                           class="p-2 text-gray-400 hover:text-amber-500 transition-colors rounded-lg hover:bg-amber-50"
                                           title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        
                                        @if(!$address->is_default)
                                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50"
                                                        title="Supprimer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-1 text-gray-700">
                                    <p class="font-semibold text-amber-900">{{ $address->nom }}</p>
                                    <p>{{ $address->adresse }}</p>
                                    <p>{{ $address->code_postal }}, {{ $address->ville }}</p>
                                    <p>{{ strtoupper($address->pays) }}</p>
                                    <p class="text-sm text-gray-500 mt-2">📞 {{ $address->telephone }}</p>
                                    <p class="text-sm text-gray-500">✉️ {{ $address->email }}</p>
                                    
                                    @if($address->instructions)
                                        <div class="mt-3 pt-3 border-t border-amber-100">
                                            <p class="text-xs text-gray-600">📝 {{ $address->instructions }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if(!$address->is_default)
                                    <form action="{{ route('addresses.setDefault', $address->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium rounded-lg transition-colors border border-amber-200">
                                            Définir comme adresse par défaut
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="text-center py-16 bg-white rounded-xl shadow-md border-2 border-amber-100">
                        <div class="text-6xl mb-4">📍</div>
                        <h3 class="text-xl font-semibold text-amber-900 mb-2">Aucune adresse enregistrée</h3>
                        <p class="text-gray-600 mb-6">Ajoutez votre première adresse pour commander plus rapidement</p>
                        <a href="{{ route('addresses.create') }}"
                           class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Ajouter une adresse
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
