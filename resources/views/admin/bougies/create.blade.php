@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.bougies.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-800">Nouvelle Bougie</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.bougies.store') }}" method="POST">
            @csrf

            <!-- Référence -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="reference">
                    Référence *
                </label>
                <input type="text" name="reference" id="reference" value="{{ old('reference', $prochaineRef ?? '') }}" 
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('reference') border-red-500 @enderror"
                    required>
                @error('reference')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nom -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">
                        Nom de la bougie *
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('nom') border-red-500 @enderror"
                        required>
                    @error('nom')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parfum -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="parfum">
                        Parfum *
                    </label>
                    <input type="text" name="parfum" id="parfum" value="{{ old('parfum') }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('parfum') border-red-500 @enderror"
                        required>
                    @error('parfum')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Collection -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="collection">
                        Collection
                    </label>
                    <select name="collection" id="collection" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">-- Sélectionner --</option>
                        @foreach($collections as $key => $label)
                        <option value="{{ $key }}" {{ old('collection') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Format -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="format">
                        Format
                    </label>
                    <select name="format" id="format" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">-- Sélectionner --</option>
                        @foreach($formats as $key => $label)
                        <option value="{{ $key }}" {{ old('format') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de cire -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="type_cire">
                        Type de cire
                    </label>
                    <select name="type_cire" id="type_cire" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">-- Sélectionner --</option>
                        @foreach($typesCire as $key => $label)
                        <option value="{{ $key }}" {{ old('type_cire') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Prix -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="prix">
                        Prix (€) *
                    </label>
                    <input type="number" step="0.01" min="0" name="prix" id="prix" value="{{ old('prix') }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('prix') border-red-500 @enderror"
                        required>
                    @error('prix')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Temps de brûlure -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="temps_brulure">
                        Temps de brûlure (min)
                    </label>
                    <input type="number" min="1" name="temps_brulure" id="temps_brulure" value="{{ old('temps_brulure') }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <!-- Quantité initiale -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantite">
                        Quantité initiale
                    </label>
                    <input type="number" min="0" name="quantite" id="quantite" value="{{ old('quantite', 0) }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <!-- Seuil alerte -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="seuil_alerte">
                        Seuil d'alerte
                    </label>
                    <input type="number" min="0" name="seuil_alerte" id="seuil_alerte" value="{{ old('seuil_alerte', 5) }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                    Notes olfactives
                </label>
                <textarea name="notes" id="notes" rows="3" 
                    class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.bougies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </a>
                <button type="submit" class="bg-[#D4AF37] hover:bg-[#B8972E] text-white font-bold py-2 px-4 rounded">
                    Créer la bougie
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
