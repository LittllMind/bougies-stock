{{-- resources/views/contact.blade.php --}}

@extends('layouts.kiosque')

@section('title', 'Contact - Les bougies de Séraphie')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="text-center mb-12">
        <span class="text-5xl mb-4 block">💌</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-amber-700 mb-4">
            Parlons bougies
        </h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
            Une question sur nos créations ? Un besoin particulier ? <br class="hidden sm:block">
            Séraphie vous répond avec plaisir.
        </p>
    </div>

    <div class="grid lg:grid-cols-2 gap-12">
        
        <!-- Infos de contact -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">📍</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Localisation</h3>
                        <p class="text-gray-600">
                            L'Atelier de Séraphie<br>
                            Le Rozier, 48150<br>
                            Lozère, France
                        </p>
                        <p class="text-amber-600 text-sm mt-2">À moins de 50km des ruchers partenaires</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">📧</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                        <a href="mailto:seraphie@lesbougiesdeseraphie.fr" class="text-amber-700 hover:text-amber-800 font-medium">
                            seraphie@lesbougiesdeseraphie.fr
                        </a>
                        <p class="text-gray-500 text-sm mt-2">Réponse sous 24h ouvrées</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">🕐</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Horaires</h3>
                        <p class="text-gray-600">
                            Mardi — Samedi<br>
                            9h00 — 18h00
                        </p>
                        <p class="text-gray-500 text-sm mt-2">
                            Fermé dimanche et lundi — on récupère de la semaine 🐝
                        </p>
                    </div>
                </div>
            </div>

            <!-- Trust badges -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 border border-amber-200 text-center">
                <div class="flex justify-center gap-2 mb-3">
                    <span class="text-xl">🐝</span>
                    <span class="text-xl">🌿</span>
                    <span class="text-xl">✋</span>
                    <span class="text-xl">🇫🇷</span>
                </div>
                <p class="text-amber-700 font-medium">Fabriqué à la main en Lozère</p>
                <p class="text-gray-600 text-sm mt-1">Cire d'abeille 100% naturelle · Sans parfum ajouté</p>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
            
            <h2 class="text-2xl font-serif font-bold text-gray-900 mb-6 flex items-center gap-3">
                <span>✍️</span>
                Envoyez-nous un message
            </h2>

            <form action="{{ route('contact') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Votre nom</label>
                        <input type="text" name="nom" required value="{{ old('nom') }}"
                            class="w-full bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition @error('nom') border-red-500 @enderror"
                            placeholder="Marie Dupont"
                        >
                        @error('nom')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Votre email</label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                            class="w-full bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition @error('email') border-red-500 @enderror"
                            placeholder="marie@email.com"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Sujet</label>
                    <select name="sujet" required
                        class="w-full bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition @error('sujet') border-red-500 @enderror"
                    >
                        <option value="" {{ old('sujet') ? '' : 'selected' }}>Choisissez un sujet</option>
                        <option value="question_produit" {{ old('sujet') == 'question_produit' ? 'selected' : '' }}>Question sur un produit</option>
                        <option value="commande_personnalisee" {{ old('sujet') == 'commande_personnalisee' ? 'selected' : '' }}>Commande personnalisée</option>
                        <option value="probleme_commande" {{ old('sujet') == 'probleme_commande' ? 'selected' : '' }}>Problème de commande</option>
                        <option value="partenariat" {{ old('sujet') == 'partenariat' ? 'selected' : '' }}>Partenariat / Presse</option>
                        <option value="autre" {{ old('sujet') == 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('sujet')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Votre message</label>
                    <textarea name="message" rows="5" required
                        class="w-full bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition resize-none @error('message') border-red-500 @enderror"
                        placeholder="Bonjour Séraphie, j'aimerais savoir si..."
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white py-4 rounded-xl font-semibold transition transform hover:scale-[1.02] flex items-center justify-center gap-2"
                >
                    <span>📮</span>
                    Envoyer le message
                </button>

                <p class="text-center text-gray-500 text-sm mt-4">
                    Vos données ne seront utilisées que pour répondre à votre message.
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
