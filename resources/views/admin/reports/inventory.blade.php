<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaire - Les bougies de Séraphie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #D4AF37; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #D4AF37; color: white; }
        .alert { background-color: #ffebee; color: #c62828; }
        .summary { margin: 20px 0; padding: 20px; background-color: #F5F5DC; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>📦 Inventaire - Les bougies de Séraphie</h1>
    <p>Date: {{ now()->format('d/m/Y H:i') }}</p>
    
    <div class="summary">
        <h3>Résumé</h3>
        <p><strong>Valeur totale du stock:</strong> {{ number_format($totalValue, 2, ',', ' ') }} €</p>
        <p><strong>Alertes stock bas:</strong> {{ $lowStockCount }} produit(s)</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Nom</th>
                <th>Collection</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Seuil Alerte</th>
                <th>Valeur Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bougies as $bougie)
            <tr {{ $bougie->quantite <= $bougie->seuil_alerte ? 'class=alert' : '' }}>
                <td>{{ $bougie->reference }}</td>
                <td>{{ $bougie->nom }}</td>
                <td>{{ $bougie->collection ?? '-' }}</td>
                <td>{{ number_format($bougie->prix, 2, ',', ' ') }} €</td>
                <td>{{ $bougie->quantite }}</td>
                <td>{{ $bougie->seuil_alerte }}</td>
                <td>{{ number_format($bougie->quantite * $bougie->prix, 2, ',', ' ') }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>