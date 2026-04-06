<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Paolo Wash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Paolo Wash - Admin</h1>
            <a href="/" class="hover:underline">Retour site</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Réservations</h2>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Client</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Prestation</th>
                        <th class="px-4 py-2 text-left">Prix</th>
                        <th class="px-4 py-2 text-left">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $reservation->client_nom }}</td>
                        <td class="px-4 py-2">{{ $reservation->date }} {{ $reservation->heure }}</td>
                        <td class="px-4 py-2">{{ $reservation->prestation }}</td>
                        <td class="px-4 py-2">{{ $reservation->prix }}€</td>
                        <td class="px-4 py-2">{{ $reservation->statut }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucune réservation</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="/admin/calendar" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Voir Calendrier</a>
        </div>
    </div>
</body>
</html>
