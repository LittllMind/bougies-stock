@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.bougies.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-800">Modifier: {{ $bougie->nom }}</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Formulaire principal de modification -->
        <form action="{{ route('admin.bougies.update', $bougie) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Référence -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="reference">
                    Référence *
                </label>
                <input type="text" name="reference" id="reference" value="{{ old('reference', $bougie->reference) }}" 
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
                        Nom *
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $bougie->nom) }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('nom') border-red-500 @enderror"
                        required>
                </div>

                <!-- Parfum -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="parfum">
                        Parfum *
                    </label>
                    <input type="text" name="parfum" id="parfum" value="{{ old('parfum', $bougie->parfum) }}" 
                        class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        required>
                </div>
            </div>

            <!-- Collections, formats, type cire -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="collection">Collection</label>
                    <select name="collection" id="collection" class="shadow border rounded w-full py-2 px-3">
                        <option value="" {{ old('collection', $bougie->collection) ? '' : 'selected' }}>-- Sélectionner --</option>
                        @foreach($collections as $key => $label)
                            <option value="{{ $key }}" {{ old('collection', $bougie->collection) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="format">Format</label>
                    <select name="format" id="format" class="shadow border rounded w-full py-2 px-3">
                        @foreach($formats as $key => $label)
                            <option value="{{ $key }}" {{ old('format', $bougie->format) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="type_cire">Type de cire</label>
                    <select name="type_cire" id="type_cire" class="shadow border rounded w-full py-2 px-3">
                        @foreach($typesCire as $key => $label)
                            <option value="{{ $key }}" {{ old('type_cire', $bougie->type_cire) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Prix, temps, quantité, seuil -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="prix">Prix (€) *</label>
                    <input type="number" step="0.01" name="prix" id="prix" value="{{ old('prix', $bougie->prix) }}" class="shadow border rounded w-full py-2 px-3" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="temps_brulure">Temps (min)</label>
                    <input type="number" name="temps_brulure" id="temps_brulure" value="{{ old('temps_brulure', $bougie->temps_brulure) }}" class="shadow border rounded w-full py-2 px-3">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantite">Stock</label>
                    <input type="number" name="quantite" id="quantite" value="{{ old('quantite', $bougie->quantite) }}" class="shadow border rounded w-full py-2 px-3">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="seuil_alerte">Seuil</label>
                    <input type="number" name="seuil_alerte" id="seuil_alerte" value="{{ old('seuil_alerte', $bougie->seuil_alerte) }}" class="shadow border rounded w-full py-2 px-3">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="shadow border rounded w-full py-2 px-3">{{ old('notes', $bougie->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <form action="{{ route('admin.bougies.destroy', $bougie) }}" method="POST" 
                          onsubmit="return confirm('Confirmer la suppression de cette bougie ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                            🗑️ Supprimer
                        </button>
                    </form>
                </div>

                <div class="flex">
                    <a href="{{ route('admin.bougies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Annuler</a>
                    <button type="submit" class="bg-[#D4AF37] hover:bg-[#B8972E] text-white font-bold py-2 px-4 rounded" style="background-color: #D4AF37">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
