<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire des Bougies - Les bougies de Séraphie</title>
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
        
        tr.alert {
            background: #ffebee;
        }
        
        tr.alert td.quantite {
            color: #c62828;
            font-weight: bold;
        }
        
        .quantite {
            text-align: center;
        }
        
        .prix {
            text-align: right;
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
        
        .seuil {
            font-size: 8pt;
            color: #999;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-alert {
            background: #c62828;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Les bougies de Séraphie</h1>
        <div class="subtitle">Rapport d'inventaire des bougies</div>
        <div style="font-size: 10pt; color: #999; margin-top: 10px;">Généré le {{ $date }}</div>
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Références uniques</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($stats['valeur_stock'], 2, ',', ' ') }} €</div>
            <div class="stat-label">Valeur du stock</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stats['alertes'] }}</div>
            <div class="stat-label">Alertes stock</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Nom</th>
                <th>Collection</th>
                <th>Parfum</th>
                <th class="prix">Prix</th>
                <th class="quantite">Stock</th>
                <th>Alerte</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bougies as $bougie)
                <tr class="{{ $bougie->quantite <= $bougie->seuil_alerte ? 'alert' : '' }}">
                    <td>{{ $bougie->reference }}</td>
                    <td>{{ $bougie->nom }}</td>
                    <td>{{ $bougie->collection ?? '-' }}</td>
                    <td>{{ $bougie->parfum }}</td>
                    <td class="prix">{{ number_format($bougie->prix, 2, ',', ' ') }} €</td>
                    <td class="quantite">{{ $bougie->quantite }}</td>
                    <td>
                        @if ($bougie->quantite <= $bougie->seuil_alerte)
                            <span class="badge badge-alert">⚠ STOCK FAIBLE</span>
                        @else
                            <span class="seuil">seuil: {{ $bougie->seuil_alerte }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Les bougies de Séraphie - 🐝 100% cire d'abeille naturelle - Fabriquées main</p>
        <p>Ce rapport est généré automatiquement et est confidentiel.</p>
    </div>
</body>
</html>
