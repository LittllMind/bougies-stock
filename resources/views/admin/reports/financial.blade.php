<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier - Les bougies de Séraphie</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #D4AF37;
            font-size: 24pt;
            margin: 0 0 5px 0;
        }
        
        .header .subtitle {
            font-size: 12pt;
            color: #666;
        }
        
        .period {
            background: #F5F5DC;
            padding: 10px;
            margin: 20px 0;
            border-left: 4px solid #D4AF37;
            font-size: 12pt;
        }
        
        /* Stats boxes */
        .stats-container {
            display: block;
            margin-bottom: 20px;
        }
        
        .stat-box {
            width: 30%;
            display: inline-block;
            background: #F5F5DC;
            border: 1px solid #D4AF37;
            padding: 10px;
            margin-right: 2%;
            text-align: center;
        }
        
        .stat-box:last-child {
            margin-right: 0;
        }
        
        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #D4AF37;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #666;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background: #D4AF37;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }
        
        tr:nth-child(even) {
            background: #fafafa;
        }
        
        .date {
            white-space: nowrap;
        }
        
        .reference {
            font-family: monospace;
            font-size: 9pt;
            color: #666;
        }
        
        .total {
            text-align: right;
            font-weight: bold;
        }
        
        .status-paid {
            color: #228B22;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .summary {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
        }
        
        .summary h3 {
            color: #D4AF37;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Les bougies de Séraphie</h1>
        <div class="subtitle">Rapport financier des ventes</div>
        <div style="font-size: 10pt; color: #999; margin-top: 10px;">Généré le {{ $date_generation }}</div>
    </div>

    <div class="period">
        <strong>Période analysée :</strong> du {{ $debut }} au {{ $fin }}
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['commandes'] }}</div>
            <div class="stat-label">Commandes payées</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($stats['total_ventes'], 2, ',', ' ') }} €</div>
            <div class="stat-label">Chiffre d'affaires</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($stats['moyenne_commande'], 2, ',', ' ') }} €</div>
            <div class="stat-label">Panier moyen</div>
        </div>
    </div>

    @if ($orders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Articles</th>
                    <th class="total">Total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="date">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="reference">{{ $order->reference }}</td>
                        <td>{{ $order->user?->name ?? '--' }}</td>
                        <td>{{ $order->items->count() }} article(s)</td>
                        <td class="total">{{ number_format($order->total, 2, ',', ' ') }} €</td>
                        <td><span class="status-{{ $order->statut }}">{{ ucfirst($order->statut) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>🧾 Récapitulatif</h3>
            <p><strong>Période :</strong> {{ $debut }} - {{ $fin }}</p>
            <p><strong>Nombre de commandes :</strong> {{ $stats['commandes'] }}</p>
            <p><strong>Chiffre d'affaires total :</strong> {{ number_format($stats['total_ventes'], 2, ',', ' ') }} €</p>
            <p><strong>Panier moyen :</strong> {{ number_format($stats['moyenne_commande'], 2, ',', ' ') }} €</p>
        </div>
    @else
        <div class="summary" style="text-align: center; color: #666;">
            <p>Aucune commande payée sur cette période.</p>
        </div>
    @endif

    <div class="footer">
        <p>Les bougies de Séraphie - 🐝 100% cire d'abeille naturelle - Fabriquées main</p>
        <p>Ce rapport est généré automatiquement et est confidentiel.</p>
    </div>
</body>
</html>
