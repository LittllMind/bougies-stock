<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Présences - Paolo Wash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Paolo Wash - Admin</h1>
            <div class="space-x-4">
                <a href="/admin/dashboard" class="hover:underline">Réservations</a>
                <a href="/admin/presences" class="hover:underline">Présences</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Formulaire nouvelle présence -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Nouvelle présence</h2>
                <form action="/admin/presences" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Lieu</label>
                        <select name="lieu_id" class="w-full border rounded p-2" required>
                            @foreach($lieux as $lieu)
                            <option value="{{ $lieu->id }}">{{ $lieu->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium">Date</label>
                        <input type="date" name="date" class="w-full border rounded p-2" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Début</label>
                            <input type="time" name="heure_debut" value="09:00" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Fin</label>
                            <input type="time" name="heure_fin" value="19:00" class="w-full border rounded p-2" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Ajouter présence
                    </button>
                </form>
            </div>

            <!-- Liste présences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Présences récentes</h2>
                
                <div class="space-y-2">
                    @forelse($presences as $presence)
                    <div class="border rounded p-3 {{ $presence->date == today() ? 'bg-green-50 border-green-300' : '' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold">{{ $presence->lieu->nom }}</p>
                                <p class="text-sm text-gray-600">{{ $presence->date }} — {{ $presence->heure_debut }} à {{ $presence->heure_fin }}</p>
                            </div>
                            @if($presence->date == today())
                            <span class="text-green-600 font-bold text-sm">Aujourd'hui</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">Aucune présence enregistrée</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
