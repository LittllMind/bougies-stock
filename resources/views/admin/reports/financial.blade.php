<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Financier - Les bougies de Séraphie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #D4AF37; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #D4AF37; color: white; }
        .summary { margin: 20px 0; padding: 20px; background-color: #F5F5DC; border-radius: 8px; }
        .total { font-size: 1.2em; color: #228B22; }
    </style>
</head>
<body>
    <h1>💰 Rapport Financier - Les bougies de Séraphie</h1>
    <p>Période: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    <p>Date du rapport: {{ now()->format('d/m/Y H:i') }}</p>
    
    <div class="summary">
        <h3>Résumé</h3>
        <p><strong>Total des commandes:</strong> {{ $totalOrders }}</p>
        <p class="total"><strong>Chiffre d'affaires:</strong> {{ number_format($totalRevenue, 2, ',', ' ') }} €</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Date</th>
                <th>Client</th>
                <th>Statut</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->reference }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->user->email ?? 'Anonyme' }}</td>
                <td>{{ $order->statut }}</td>
                <td>{{ number_format($order->total, 2, ',', ' ') }} €</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>{{ number_format($totalRevenue, 2, ',', ' ') }} €</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>