<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catalogue - Les bougies de Séraphie</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 text-gray-100">
    <div id="app">
        <nav class="bg-gray-800">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl">🕯️</span>
                        <span class="text-amber-400 font-bold text-xl ml-3">Les bougies de Séraphie</span>
                    </a>
                    
                    <div class="flex items-center space-x-8">
                        <a href="/" class="text-gray-300 hover:text-amber-400">Accueil</a>
                        <span class="text-amber-400">Nos Bougies</span>
                        <a href="/cart" class="bg-amber-500 text-gray-900 px-4 py-2 rounded-xl">🛒 Panier</a>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-gradient-to-b from-gray-800 to-gray-900 py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-amber-400 mb-4">
                    🕯️ Nos Bougies Artisanales
                </h1>
                <p class="text-gray-400 text-lg">
                    Cire d'abeille 100% naturelle, façonnée à la main dans notre atelier
                </p>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8" id="catalogue-app">
            <!-- Vue.js s'initialisera ici -->
            <div class="text-center text-gray-400 py-20">
                Chargement du catalogue...
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const { createApp } = Vue;
        
        const API_URL = '/api/catalogue/bougies';
        
        createApp({
            setup() {
                return {
                    message: 'Catalogue Vue.js fonctionnel'
                };
            }
        }).mount('#catalogue-app');
    </script>
</body>
</html>