@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-seph-warm py-20 pt-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl sm:text-5xl font-serif font-bold text-center mb-12 text-gray-900">
            <span class="text-amber-700">L'Atelier</span>
        </h1>

        <div class="space-y-12">
            <!-- Qui est Séraphie -->
            <section class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <h2 class="text-2xl font-serif font-bold mb-6 flex items-center text-gray-900">
                    <span class="text-3xl mr-3">🐝</span>
                    Qui est Séraphie ?
                </h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-6">
                    Séraphie, c'est moi. Artisane bougie depuis 2018, j'ai découvert la cire d'abeille par amour pour ces petites bêtes si essentielles. 
                    Ce qui a commencé comme une passion est devenu mon métier : créer des bougies qui respectent votre santé autant que notre planète.
                </p>
                <p class="text-amber-700 text-lg italic">
                    "Je ne fais pas de parfums complexes. La cire d'abeille a déjà son propre parfum, subtil et réconfortant. 
                    Pourquoi en rajouter ?"
                </p>
            </section>

            <!-- Notre Engagement -->
            <section class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <h2 class="text-2xl font-serif font-bold mb-6 flex items-center text-gray-900">
                    <span class="text-3xl mr-3">🌿</span>
                    Notre engagement
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h3 class="font-semibold mb-1">100% Cire d'abeille</h3>
                                <p class="text-gray-600 text-sm">Pas de soja, pas de colza, pas de paraffine. Juste la cire dorée des abeilles.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h3 class="font-semibold mb-1">Sans parfum ajouté</h3>
                                <p class="text-gray-600 text-sm">Pas d'huiles essentielles synthétiques, pas d'arômes chimiques.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h3 class="font-semibold mb-1">Circuit court</h3>
                                <p class="text-gray-600 text-sm">Nos ruchiers sont à moins de 50km de l'atelier. Production locale et tracée.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h3 class="font-semibold mb-1">Fait main</h3>
                                <p class="text-gray-600 text-sm">Chaque bougie est coulée, démoulée et finie à la main par mes soins.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Le Processus -->
            <section class="bg-white rounded-2xl p-8 border border-amber-100 shadow-sm">
                <h2 class="text-2xl font-serif font-bold mb-6 flex items-center text-gray-900">
                    <span class="text-3xl mr-3">🙌</span>
                    Le processus artisanal
                </h2>
                <div class="space-y-6 text-gray-700">
                    <div class="flex items-start">
                        <span class="bg-amber-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1 flex-shrink-0">1</span>
                        <div>
                            <h3 class="font-semibold mb-1">Sélection de la cire</h3>
                            <p>Chaque lot de cire est contrôlé : couleur dorée, odeur de miel frais, pureté garantie. Je travaille uniquement avec des cires brutes non raffinées.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-amber-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1 flex-shrink-0">2</span>
                        <div>
                            <h3 class="font-semibold mb-1">Filtrage doux</h3>
                            <p>La cire est fondue et filtrée pour éliminer les impuretés mécaniques, tout en conservant son parfum naturel et ses propriétés bénéfiques.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-amber-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1 flex-shrink-0">3</span>
                        <div>
                            <h3 class="font-semibold mb-1">Coulage à la main</h3>
                            <p>Chaque moule est préparé avec soin. La cire est versée à température contrôlée pour préserver sa structure.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-amber-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1 flex-shrink-0">4</span>
                        <div>
                            <h3 class="font-semibold mb-1">Finition et contrôle</h3>
                            <p>Après refroidissement, chaque bougie est démoulée, inspectée, et sa surface est légèrement polie pour révéler son éclat doré naturel.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pourquoi ça compte -->
            <section class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 border border-amber-200">
                <h2 class="text-2xl font-serif font-bold mb-6 flex items-center text-gray-900">
                    <span class="text-3xl mr-3">💚</span>
                    Pourquoi ça compte
                </h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-6">
                    Les bougies commerciales contiennent souvent des paraffines (dérivés du pétrole), 
                    des parfums synthétiques et des colorants artificiels. À la combustion, elles libèrent 
                    des substances potentiellement nocives.
                </p>
                <p class="text-amber-700 text-lg leading-relaxed">
                    Nos bougies en cire d'abeille brûlent proprement, sans fumée noire ni odeur chimique. 
                    Elles purifient l'air au lieu de le polluer. C'est un choix pour votre santé, 
                    pour votre maison, et pour les abeilles dont nous dépendons tous.
                </p>
            </section>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('kiosque.index') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition transform hover:scale-105">
                Découvrir les créations
            </a>
        </div>
    </div>
</div>
@endsection
