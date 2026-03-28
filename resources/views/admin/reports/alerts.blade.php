<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alertes Stock - Les bougies de Séraphie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #D4AF37; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #D4AF37; color: white; }
        .alert { background-color: #ffebee; }
        .urgent { background-color: #ffcdd2; color: #b71c1c; font-weight: bold; }
        .summary { margin: 20px 0; padding: 20px; background-color: #F5F5DC; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>🚨 Alertes Stock - Les bougies de Séraphie</h1>
    <p>Date du rapport: {{ now()->format('d/m/Y H:i') }}</p>
    
    <div class="summary">
        <h3>Résumé</h3>
        <p><strong>Produits en alerte:</strong> {{ $lowStockBougies->count() }}</p>
    </div>
    
    @if($lowStockBougies->isEmpty())
        <p>✅ Aucune alerte stock. Tous les produits sont bien approvisionnés.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Nom</th>
                    <th>Collection</th>
                    <th>Quantité</th>
                    <th>Seuil Alerte</th>
                    <th>Manquant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockBougies as $bougie)
                <tr class="{{ $bougie->quantite == 0 ? 'urgent' : 'alert' }}">
                    <td>{{ $bougie->reference }}</td>
                    <td>{{ $bougie->nom }}</td>
                    <td>{{ $bougie->collection ?? '-' }}</td>
                    <td>{{ $bougie->quantite }}</td>
                    <td>{{ $bougie->seuil_alerte }}</td>
                    <td>{{ $bougie->seuil_alerte - $bougie->quantite }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>