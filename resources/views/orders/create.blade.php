@extends('layouts.app')

@section('title', 'Commande - Livraison')

@section('content')
<div x-data="{ 
    facturationDifferent: false,
    useSameAddress: true,
    selectedAddressId: '',
    saveNewAddress: false,
    addressLabel: 'Maison',
    
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
}" class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">
                Finaliser votre commande
            </h1>
            <p class="mt-2 text-gray-400">Étape 2/3 : Informations de livraison</p>
        </div>

        <!-- Progression -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm">✓</div>
                    <span class="ml-2 text-green-400 text-sm">Panier</span>
                </div>
                <div class="w-16 h-1 bg-violet-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-violet-500 flex items-center justify-center text-white text-sm font-bold">2</div>
                    <span class="ml-2 text-violet-400 text-sm font-semibold">Livraison</span>
                </div>
                <div class="w-16 h-1 bg-gray-700"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-sm">3</div>
                    <span class="ml-2 text-gray-500 text-sm">Paiement</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire de livraison -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-6">📍 Adresse de livraison</h2>

                    @if(Auth::check() && $addresses->count() > 0)
                        <!-- Sélection d'adresse existante -->
                        <div class="mb-6 p-4 bg-gray-900 rounded-xl border border-gray-700">
                            <label for="address_select" class="block text-sm font-medium text-violet-400 mb-2">
                                Choisir une adresse enregistrée
                            </label>
                            <select id="address_select" 
                                    x-model="selectedAddressId"
                                    @change="loadAddress(selectedAddressId)"
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
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
                                           class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-violet-500 focus:ring-2 focus:ring-violet-500">
                                    <span class="text-sm text-gray-300">💾 Sauvegarder cette adresse</span>
                                </label>
                                
                                <input type="text" 
                                       name="address_label" 
                                       x-model="addressLabel"
                                       x-show="saveNewAddress"
                                       placeholder="Label (Maison, Travail...)"
                                       class="px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-sm text-white focus:ring-2 focus:ring-violet-500">
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Informations personnelles -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-violet-400">Informations personnelles</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-300 mb-1">Nom *</label>
                                    <input type="text" id="nom" name="nom" required
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                        placeholder="Votre nom"
                                        value="{{ auth()->user()->name ?? '' }}">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email *</label>
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                        placeholder="votre@email.com"
                                        value="{{ auth()->user()->email ?? '' }}">
                                </div>
                            </div>

                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-300 mb-1">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" required
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                    placeholder="06 12 34 56 78">
                            </div>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="space-y-4 pt-4 border-t border-gray-700">
                            <h3 class="text-lg font-semibold text-violet-400">📍 Adresse de livraison</h3>

                            <div>
                                <label for="livraison_adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse *</label>
                                <input type="text" id="livraison_adresse" name="adresse" required
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                    placeholder="123 Rue de la Musique">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="livraison_code_postal" class="block text-sm font-medium text-gray-300 mb-1">Code postal *</label>
                                    <input type="text" id="livraison_code_postal" name="code_postal" required pattern="[0-9]{5}"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                        placeholder="75001">
                                </div>

                                <div>
                                    <label for="livraison_ville" class="block text-sm font-medium text-gray-300 mb-1">Ville *</label>
                                    <input type="text" id="livraison_ville" name="ville" required
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                        placeholder="Paris">
                                </div>
                            </div>

                            <div>
                                <label for="livraison_pays" class="block text-sm font-medium text-gray-300 mb-1">Pays *</label>
                                <select id="livraison_pays" name="pays" required
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                                    <option value="FR">France</option>
                                    <option value="BE">Belgique</option>
                                    <option value="CH">Suisse</option>
                                    <option value="LU">Luxembourg</option>
                                    <option value="DE">Allemagne</option>
                                    <option value="OTHER">Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Instructions spéciales -->
                        <div class="space-y-4 pt-4 border-t border-gray-700">
                            <h3 class="text-lg font-semibold text-violet-400">Instructions de livraison</h3>

                            <div>
                                <label for="instructions" class="block text-sm font-medium text-gray-300 mb-1">Instructions (optionnel)</label>
                                <textarea id="instructions" name="instructions" rows="3"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"
                                    placeholder="Code d'accès, digicode, étage, etc."></textarea>
                            </div>
                        </div>

                        <!-- Option : Adresse de facturation différente -->
                        <div class="pt-4 border-t border-gray-700">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       id="use_same_address" 
                                       name="use_same_address" 
                                       value="0"
                                       x-model="useSameAddress"
                                       class="w-5 h-5 rounded border-gray-600 bg-gray-900 text-violet-500 focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition">
                                <span class="text-gray-300 font-medium">📄 Adresse de facturation différente</span>
                            </label>
                        </div>

                        <!-- Adresse de facturation (conditionnelle) -->
                        <div x-show="!useSameAddress" x-cloak class="space-y-4 pt-4 border-t border-gray-700">
                            <h3 class="text-lg font-semibold text-pink-400">💳 Adresse de facturation</h3>

                            <div>
                                <label for="facturation_nom" class="block text-sm font-medium text-gray-300 mb-1">Nom</label>
                                <input type="text" id="facturation_nom" name="facturation_nom"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                    placeholder="Nom sur la facture">
                            </div>

                            <div>
                                <label for="facturation_adresse" class="block text-sm font-medium text-gray-300 mb-1">Adresse</label>
                                <input type="text" id="facturation_adresse" name="facturation_adresse"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                    placeholder="Adresse de facturation">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="facturation_code_postal" class="block text-sm font-medium text-gray-300 mb-1">Code postal</label>
                                    <input type="text" id="facturation_code_postal" name="facturation_code_postal" pattern="[0-9]{5}"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="75001">
                                </div>

                                <div>
                                    <label for="facturation_ville" class="block text-sm font-medium text-gray-300 mb-1">Ville</label>
                                    <input type="text" id="facturation_ville" name="facturation_ville"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="Paris">
                                </div>
                            </div>

                            <div>
                                <label for="facturation_pays" class="block text-sm font-medium text-gray-300 mb-1">Pays</label>
                                <select id="facturation_pays" name="facturation_pays"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all">
                                    <option value="FR">France</option>
                                    <option value="BE">Belgique</option>
                                    <option value="CH">Suisse</option>
                                    <option value="LU">Luxembourg</option>
                                    <option value="DE">Allemagne</option>
                                    <option value="OTHER">Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <a href="{{ route('cart.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-xl transition-colors text-center">
                                ← Retour au panier
                            </a>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white font-semibold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                                Continuer vers le paiement →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Récapitulatif panier -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-700 sticky top-8">
                    <h2 class="text-xl font-bold text-white mb-4">Récapitulatif</h2>

                    @if($cart->items->count() > 0)
                        <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between py-2 border-b border-gray-700">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-white">{{ $item->vinyle->titre ?? 'Vinyle inconnu' }}</p>
                                        <p class="text-xs text-gray-400">Qté: {{ $item->quantite }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-violet-400">
                                        {{ number_format($item->prix_unitaire * $item->quantite, 2) }} €
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totaux -->
                        <div class="space-y-2 pt-4 border-t border-gray-700">
                            <div class="flex justify-between text-sm text-gray-400">
                                <span>Sous-total</span>
                                <span>{{ number_format($cart->total, 2) }} €</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-400">
                                <span>Livraison</span>
                                <span class="text-green-400">Gratuite</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-white pt-2 border-t border-gray-700">
                                <span>Total</span>
                                <span class="bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">
                                    {{ number_format($cart->total, 2) }} €
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-400">Votre panier est vide</p>
                            <a href="{{ route('kiosque.index') }}"
                                class="inline-block mt-4 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-xl transition-colors">
                                Découvrir nos vinyles
                            </a>
                        </div>
                    @endif

                    <!-- Sécurité -->
                    <div class="mt-6 pt-4 border-t border-gray-700">
                        <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
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
