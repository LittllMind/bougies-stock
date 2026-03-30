@extends('layouts.app')

@section('title', 'Commande - Livraison')

@section('content')
<div x-data="{ 
    facturationDifferent: {{ ($tempBilling && $tempBilling != $tempShipping) || old('use_same_address') === '0' ? 'true' : 'false' }},
    useSameAddress: {{ old('use_same_address') === '0' ? 'false' : 'true' }},
    selectedAddressId: '',
    saveNewAddress: {{ old('save_address') ? 'true' : 'false' }},
    addressLabel: '{{ old('address_label', 'Maison') }}',
    
    loadAddress(addressId) {
        if (!addressId) return;
        
        const addresses = @js($addresses);
        const address = addresses.find(a => a.id == addressId);
        
        if (address) {
            document.getElementById('nom').value = address.nom;
            document.getElementById('email').value = address.email;
            document.getElementById('telephone').value = address.telephone;
            document.getElementById('livraison_adresse').value = address.adresse;
            document.getElementById('livraison_code_postal').value = address.code_postal;
            document.getElementById('livraison_ville').value = address.ville;
            document.getElementById('livraison_pays').value = address.pays;
            document.getElementById('instructions').value = address.instructions || '';
        }
    }
}" class="min-h-screen bg-amber-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-amber-900" style="font-family: 'Cormorant Garamond', serif;">
                Finaliser votre commande
            </h1>
            <p class="mt-2 text-amber-600">Étape 2/3 : Informations de livraison</p>
        </div>

        <!-- Progression -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white text-sm">✓</div>
                    <span class="ml-2 text-green-700 text-sm">Panier</span>
                </div>
                <div class="w-16 h-1 bg-amber-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center text-white text-sm font-bold">2</div>
                    <span class="ml-2 text-amber-700 text-sm font-semibold">Livraison</span>
                </div>
                <div class="w-16 h-1 bg-amber-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-amber-300 flex items-center justify-center text-amber-900 text-sm">3</div>
                    <span class="ml-2 text-amber-600 text-sm">Paiement</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire de livraison -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200">
                    <h2 class="text-2xl font-bold text-amber-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📍</span> Adresse de livraison
                    </h2>

                    @if(Auth::check() && $addresses->count() > 0)
                        <!-- Sélection d'adresse existante -->
                        <div class="mb-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
                            <label for="address_select" class="block text-sm font-medium text-amber-700 mb-2">
                                Choisir une adresse enregistrée
                            </label>
                            <select id="address_select" 
                                    x-model="selectedAddressId"
                                    @change="loadAddress(selectedAddressId)"
                                    class="w-full px-4 py-3 bg-white border border-amber-300 rounded-xl text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                                <option value="">-- Nouvelle adresse --</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                        {{ $address->label }} - {{ $address->adresse }}, {{ $address->code_postal }} {{ $address->ville }}
                                        {{ $address->is_default ? ' (Par défaut)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <div class="mt-4 flex items-center space-x-4">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" 
                                           name="save_address" 
                                           value="1"
                                           x-model="saveNewAddress"
                                           class="w-4 h-4 rounded border-amber-300 bg-white text-amber-600 focus:ring-2 focus:ring-amber-500">
                                    <span class="text-sm text-amber-800">💾 Sauvegarder cette adresse</span>
                                </label>
                                
                                <input type="text" 
                                       name="address_label" 
                                       x-model="addressLabel"
                                       x-show="saveNewAddress"
                                       placeholder="Label (Maison, Travail...)"
                                       class="px-3 py-2 bg-white border border-amber-300 rounded-lg text-sm text-amber-900 focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Informations personnelles -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-amber-700">Informations personnelles</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-amber-800 mb-1">Nom *</label>
                                    <input type="text" id="nom" name="nom" required
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="Votre nom"
                                        value="{{ old('nom', $tempShipping['nom'] ?? auth()->user()->name ?? '') }}">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-amber-800 mb-1">Email *</label>
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="votre@email.com"
                                        value="{{ old('email', $tempShipping['email'] ?? auth()->user()->email ?? '') }}">
                                </div>
                            </div>

                            <div>
                                <label for="telephone" class="block text-sm font-medium text-amber-800 mb-1">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" required
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                    placeholder="06 12 34 56 78"
                                    value="{{ old('telephone', $tempShipping['telephone'] ?? '') }}">
                            </div>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="space-y-4 pt-4 border-t border-amber-200">
                            <h3 class="text-lg font-semibold text-amber-700 flex items-center">
                                <span class="text-xl mr-2">📍</span> Adresse de livraison
                            </h3>

                            <div>
                                <label for="livraison_adresse" class="block text-sm font-medium text-amber-800 mb-1">Adresse *</label>
                                <input type="text" id="livraison_adresse" name="adresse" required
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                    placeholder="123 Rue de la Lumière"
                                    value="{{ old('adresse', $tempShipping['adresse'] ?? '') }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="livraison_code_postal" class="block text-sm font-medium text-amber-800 mb-1">Code postal *</label>
                                    <input type="text" id="livraison_code_postal" name="code_postal" required pattern="[0-9]{5}"
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="75001"
                                        value="{{ old('code_postal', $tempShipping['code_postal'] ?? '') }}">
                                </div>

                                <div>
                                    <label for="livraison_ville" class="block text-sm font-medium text-amber-800 mb-1">Ville *</label>
                                    <input type="text" id="livraison_ville" name="ville" required
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="Paris"
                                        value="{{ old('ville', $tempShipping['ville'] ?? '') }}">
                                </div>
                            </div>

                            <div>
                                <label for="livraison_pays" class="block text-sm font-medium text-amber-800 mb-1">Pays *</label>
                                <select id="livraison_pays" name="pays" required
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                                    @php $pays = old('pays', $tempShipping['pays'] ?? 'FR') @endphp
                                    <option value="FR" {{ $pays == 'FR' ? 'selected' : '' }}>France</option>
                                    <option value="BE" {{ $pays == 'BE' ? 'selected' : '' }}>Belgique</option>
                                    <option value="CH" {{ $pays == 'CH' ? 'selected' : '' }}>Suisse</option>
                                    <option value="LU" {{ $pays == 'LU' ? 'selected' : '' }}>Luxembourg</option>
                                    <option value="DE" {{ $pays == 'DE' ? 'selected' : '' }}>Allemagne</option>
                                    <option value="OTHER" {{ $pays == 'OTHER' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Instructions spéciales -->
                        <div class="space-y-4 pt-4 border-t border-amber-200">
                            <h3 class="text-lg font-semibold text-amber-700">Instructions de livraison</h3>

                            <div>
                                <label for="instructions" class="block text-sm font-medium text-amber-800 mb-1">Instructions (optionnel)</label>
                                <textarea id="instructions" name="instructions" rows="3"
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all resize-none"
                                    placeholder="Code d'accès, digicode, étage, etc.">{{ old('instructions', $tempShipping['instructions'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Option : Adresse de facturation différente -->
                        <div class="pt-4 border-t border-amber-200">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="hidden" name="use_same_address" value="1">
                                <input type="checkbox" 
                                       id="facturation_differente" 
                                       name="use_same_address" 
                                       value="0"
                                       x-model="facturationDifferent"
                                       :checked="facturationDifferent"
                                       class="w-5 h-5 rounded border-amber-300 bg-white text-amber-600 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-amber-50 transition">
                                <span class="text-amber-800 font-medium">📄 Adresse de facturation différente</span>
                            </label>
                        </div>

                        <!-- Adresse de facturation (conditionnelle) -->
                        <div x-show="facturationDifferent" x-cloak class="space-y-4 pt-4 border-t border-amber-200">
                            <h3 class="text-lg font-semibold text-amber-700 flex items-center">
                                <span class="text-xl mr-2">💳</span> Adresse de facturation
                            </h3>

                            <div>
                                <label for="facturation_nom" class="block text-sm font-medium text-amber-800 mb-1">Nom</label>
                                <input type="text" id="facturation_nom" name="facturation_nom"
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                    placeholder="Nom sur la facture"
                                    value="{{ old('facturation_nom', $tempBilling['nom'] ?? '') }}">
                            </div>

                            <div>
                                <label for="facturation_adresse" class="block text-sm font-medium text-amber-800 mb-1">Adresse</label>
                                <input type="text" id="facturation_adresse" name="facturation_adresse"
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                    placeholder="Adresse de facturation"
                                    value="{{ old('facturation_adresse', $tempBilling['adresse'] ?? '') }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="facturation_code_postal" class="block text-sm font-medium text-amber-800 mb-1">Code postal</label>
                                    <input type="text" id="facturation_code_postal" name="facturation_code_postal" pattern="[0-9]{5}"
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="75001"
                                        value="{{ old('facturation_code_postal', $tempBilling['code_postal'] ?? '') }}">
                                </div>

                                <div>
                                    <label for="facturation_ville" class="block text-sm font-medium text-amber-800 mb-1">Ville</label>
                                    <input type="text" id="facturation_ville" name="facturation_ville"
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 placeholder-amber-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        placeholder="Paris"
                                        value="{{ old('facturation_ville', $tempBilling['ville'] ?? '') }}">
                                </div>
                            </div>

                            <div>
                                <label for="facturation_pays" class="block text-sm font-medium text-amber-800 mb-1">Pays</label>
                                <select id="facturation_pays" name="facturation_pays"
                                    class="w-full px-4 py-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                                    @php $factPays = old('facturation_pays', $tempBilling['pays'] ?? 'FR') @endphp
                                    <option value="FR" {{ $factPays == 'FR' ? 'selected' : '' }}>France</option>
                                    <option value="BE" {{ $factPays == 'BE' ? 'selected' : '' }}>Belgique</option>
                                    <option value="CH" {{ $factPays == 'CH' ? 'selected' : '' }}>Suisse</option>
                                    <option value="LU" {{ $factPays == 'LU' ? 'selected' : '' }}>Luxembourg</option>
                                    <option value="DE" {{ $factPays == 'DE' ? 'selected' : '' }}>Allemagne</option>
                                    <option value="OTHER" {{ $factPays == 'OTHER' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <a href="{{ route('cart.index') }}"
                                class="flex-1 px-6 py-3 bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold rounded-xl transition-colors text-center">
                                ← Retour au panier
                            </a>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-500 hover:from-amber-500 hover:to-orange-400 text-white font-semibold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                                Continuer vers le paiement →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Récapitulatif panier -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-200 sticky top-8">
                    <h2 class="text-xl font-bold text-amber-900 mb-4">Récapitulatif</h2>

                    @if(count($items) > 0)
                        <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                            @foreach($items as $item)
                                <div class="flex items-center justify-between py-2 border-b border-amber-200">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-amber-900">{{ $item['nom'] }}</p>
                                        <p class="text-xs text-amber-600">Qté: {{ $item['quantite'] }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-amber-700">
                                        {{ number_format($item['sous_total'], 2) }} €
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totaux -->
                        <div class="space-y-2 pt-4 border-t border-amber-200">
                            <div class="flex justify-between text-sm text-amber-700">
                                <span>Sous-total</span>
                                <span>{{ number_format($total, 2) }} €</span>
                            </div>
                            <div class="flex justify-between text-sm text-amber-700">
                                <span>Livraison</span>
                                <span class="text-green-600 font-medium">Gratuite</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-amber-900 pt-2 border-t border-amber-200">
                                <span>Total</span>
                                <span class="text-amber-700">
                                    {{ number_format($total, 2) }} €
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-amber-600">Votre panier est vide</p>
                            <a href="{{ route('kiosque') }}"
                                class="inline-block mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl transition-colors">
                                Découvrir nos bougies
                            </a>
                        </div>
                    @endif

                    <!-- Sécurité -->
                    <div class="mt-6 pt-4 border-t border-amber-200">
                        <div class="flex items-center justify-center space-x-2 text-xs text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Paiement 100% sécurisé</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
