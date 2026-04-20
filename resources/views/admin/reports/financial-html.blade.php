<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Financier - Les bougies de Séraphie</title>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #D4AF37;
            font-size: 28pt;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            font-size: 14pt;
            color: #666;
        }
        
        .period {
            background: #F5F5DC;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #D4AF37;
            font-size: 14pt;
            text-align: center;
        }
        
        /* Stats boxes */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-box {
            flex: 1;
            min-width: 200px;
            background: #F5F5DC;
            border: 2px solid #D4AF37;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }
        
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            color: #D4AF37;
            margin: 0;
        }
        
        .stat-label {
            font-size: 11pt;
            color: #666;
            margin-top: 5px;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        th {
            background: #D4AF37;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 11pt;
            font-weight: 600;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }
        
        tr:nth-child(even) {
            background: #fafafa;
        }
        
        tr:hover {
            background: #f5f5dc;
        }
        
        .date { white-space: nowrap; }
        .reference { font-family: monospace; font-size: 9pt; }
        .amount { text-align: right; font-weight: bold; }
        .client { font-style: italic; }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 10pt;
        }
        
        .btn-print {
            background: #D4AF37;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 12pt;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px auto;
            display: block;
        }
        
        .btn-print:hover {
            background: #B8941F;
        }
        
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state-icon {
            font-size: 48pt;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimer / Enregistrer PDF</button>
    
    <div class="header">
        <h1>💰 Rapport Financier</h1>
        <div class="subtitle">Les bougies de Séraphie</div>
    </div>
    
    <div class="period">
        📅 Période : <strong>{{ $debut }}</strong> au <strong>{{ $fin }}</strong>
    </div>
    
    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['commandes'] }}</div>
            <div class="stat-label">Commandes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($stats['total_ventes'], 2, ',', ' ') }} €</div>
            <div class="stat-label">Total Ventes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($stats['moyenne_commande'], 2, ',', ' ') }} €</div>
            <div class="stat-label">Panier Moyen</div>
        </div>
    </div>
    
    @if($orders->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Date</th>
                <th>Client</th>
                <th>Articles</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td class="reference">{{ $order->reference }}</td>
                <td class="date">{{ $order->created_at->format('d/m/Y') }}</td>
                <td class="client">{{ $order->user->prenom ?? '' }} {{ $order->user->nom ?? 'Anonyme' }}</td>
                <td>{{ $order->items->count() }} article(s)</td>
                <td class="amount">{{ number_format($order->total, 2, ',', ' ') }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📊</div>
        <p>Aucune commande payée trouvée pour cette période.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>Généré le {{ $date_generation }} — Les bougies de Séraphie</p>
        </p>Version imprimable — Utilisez le bouton ci-dessus pour enregistrer en PDF</p>
    </div>
</body>
</html>
