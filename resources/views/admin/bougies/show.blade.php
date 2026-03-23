@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.bougies.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-800">{{ $bougie->nom }}</h1>
    </div>

    <!-- Informations principales -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div>
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Informations</h2>
                
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Référence:</dt>
                        <dd class="font-mono">{{ $bougie->reference }}</dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Parfum:</dt>
                        <dd>{{ $bougie->parfum }}</dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Collection:</dt>
                        <dd>{{ $bougie->collection ?? 'Non spécifiée' }}</dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Format:</dt>
                        <dd>{{ $bougie->format ?? 'Non spécifié' }}</dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Type de cire:</dt>
                        <dd>{{ $bougie->type_cire ?? 'Non spécifié' }}</dd>
                    </div>
                    
                    @if($bougie->temps_brulure)
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Durée:</dt>
                        <dd>{{ $bougie->temps_brulure }} minutes</dd>
                    </div>
                    @endif
                </dl>
            </div>

            
<div>
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Stock & Prix</h2>
                
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Prix:</dt>
                        <dd class="text-lg font-bold text-[#D4AF37]">{{ number_format($bougie->prix, 2) }} €</dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">En stock:</dt>
                        <dd>
                            @if($bougie->rupture_stock)
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Rupture de stock</span>
                            @elseif($bougie->stock_faible)
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">{{ $bougie->quantite }} (stock faible)</span>
                            @else
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ $bougie->quantite }}</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div class="flex">
                        <dt class="w-32 font-medium text-gray-600">Seuil d'alerte:</dt>
                        <dd>{{ $bougie->seuil_alerte }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($bougie->notes)
        <div class="mt-6 border-t pt-4">
            <h3 class="font-medium text-gray-600 mb-2">Notes olfactives:</h3>
            <p class="text-gray-700">{{ $bougie->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-end">
        <a href="{{ route('admin.bougies.edit', $bougie) }}" class="bg-[#D4AF37] hover:bg-[#B8972E] text-white font-bold py-2 px-4 rounded">
            ✏️ Modifier
        </a>
    </div>
</div>
@endsection
