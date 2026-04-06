<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paolo Wash - Lavage Auto à Domicile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4">Paolo Wash</h1>
            <p class="text-2xl mb-6">Votre voiture étincelante, sans bouger</p>
            @if($presence)
                <div class="bg-white/20 rounded-xl p-4 mt-6">
                    <p class="text-lg">📍 Je suis présent aujourd'hui :</p>
                    <p class="text-2xl font-bold">{{ $presence->lieu->nom }}</p>
                    <p>{{ $presence->heure_debut }} - {{ $presence->heure_fin }}</p>
                </div>
            @else
                <p class="mt-6 opacity-80">📅 Présence non définie pour aujourd'hui</p>
            @endif
        </div>
    </div>

    <!-- Tarifs -->
    <div class="py-16 px-4 max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nos Prestations</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-4xl mb-4">🚗</div>
                <h3 class="text-xl font-bold mb-2">Express</h3>
                <p class="text-gray-600 mb-4">Lavage extérieur rapide</p>
                <p class="text-3xl font-bold text-blue-600">35€</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-4xl mb-4">✨</div>
                <h3 class="text-xl font-bold mb-2">Essentiel</h3>
                <p class="text-gray-600 mb-4">Intérieur + Extérieur</p>
                <p class="text-3xl font-bold text-blue-600">55€</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-4xl mb-4">🌟</div>
                <h3 class="text-xl font-bold mb-2">Premium</h3>
                <p class="text-gray-600 mb-4">Complet + Finitions</p>
                <p class="text-3xl font-bold text-blue-600">75€</p>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="bg-gray-100 py-16 px-4">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Contactez Paolo</h2>
            <p class="text-lg mb-4">📞 Téléphone : <a href="tel:0600000000" class="text-blue-600">06 00 00 00 00</a></p>
            <p class="text-lg">📧 Email : <a href="mailto:Paolo@wash.com" class="text-blue-600">Paolo@wash.com</a></p>
            <p class="mt-8 text-gray-600">Réservation par téléphone ou message uniquement</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 text-center">
        <p>Paolo Wash © 2026</p>
    </footer>
</body>
</html>
