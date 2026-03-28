<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Inventaire - Les bougies de Séraphie</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background: #fff;
            color: #333;
            line-height: 1.6;
            font-size: 11pt;
        }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 40px; }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #D4AF37;
        }
        .logo { font-size: 1.8rem; color: #D4AF37; margin-bottom: 5px; }
        .company-name { font-size: 1.4rem; color: #333; margin-bottom: 5px; }
        .report-title { font-size: 1.2rem; color: #666; }
        .generated-at { font-size: 0.9rem; color: #999; margin-top: 8px; }
        
        .summary-box {
            background: #F5F5DC;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #D4AF37;
        }
        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .summary-item { text-align: center; flex: 1; min-width: 140px; }
        .summary-value { font-size: 1.6rem; font-weight: bold; color: #D4AF37; }
        .summary-label { font-size: 0.85rem; color: #666; margin-top: 4px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10pt;
        }
        th {
            background: #F5F5DC;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #D4AF37;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        tr:hover { background: #fafafa; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 0.85rem;
        }
        
        .alert-header {
            background: #fff3cd;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #D4AF37;
        }
        .alert-header h2 { color: #856404; font-size: 1.2rem; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">🕯️</div>
            <div class="company-name">Les bougies de Séraphie</div>
            @if(isset($is_alerts_only) && $is_alerts_only)
                <div class="report-title">⚠️ Rapport des Alertes de Stock</div>
            @else
                <div class="report-title">📦 Rapport d'Inventaire Complet</div>
            @endif
            <div class="generated-at">Généré le {{ $generated_at->format('d/m/Y à H:i') }}</div>
        </header>
        
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">{{ $bougies->count() }}</div>
                    <div class="summary-label">Références</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $stats['total_unites'] ?? 0 }}</div>
                    <div class="summary-label">Unités en stock</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ number_format($stats['total_valeur_stock'] ?? 0, 0, ',', ' ') }} €</div>
                    <div class="summary-label">Valeur totale</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #dc3545;">{{ $stats['produits_en_alerte'] ?? 0 }}</div>
                    <div class="summary-label">En alerte</div>
                </div>
            </div>
        </div>
        
        @if(isset($is_alerts_only) && $is_alerts_only)
        <div class="alert-header">
            <h2>⚠️ Produits nécessitant une attention immédiate</h2>
        </div>
        @endif
        
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Nom</th>
                    <th>Collection</th>
                    <th class="text-right">Prix</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Seuil</th>
                    <th class="text-center">Statut</th>
                    <th class="text-right">Valeur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bougies as $bougie)
                <tr>
                    <td><strong>{{ $bougie->reference }}</strong></td>
                    <td>{{ $bougie->nom }}</td>
                    <td>{{ $bougie->collection ?: '-' }}</td>
                    <td class="text-right">{{ number_format($bougie->prix, 2, ',', ' ') }} €</td>
                    <td class="text-center">{{ $bougie->quantite }}</td>
                    <td class="text-center">{{ $bougie->seuil_alerte }}</td>
                    <td class="text-center">
                        @if($bougie->quantite <= 0)
                            <span class="badge badge-danger">Rupture</span>
                        @elseif($bougie->quantite <= $bougie->seuil_alerte)
                            <span class="badge badge-warning">Alerte</span>
                        @else
                            <span class="badge badge-success">OK</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($bougie->quantite * $bougie->prix, 2, ',', ' ') }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <footer class="footer">
            <p>Les bougies de Séraphie — L'art de la lumière pure 🐝</p>
            <p>Ce document est confidentiel et destiné à un usage interne uniquement.</p>
        </footer>
    </div>
    
    <script>
        // Auto-print when loaded, useful for PDF generation
        window.onload = function() {
            setTimeout(function() {
                window.focus();
                if (!window.location.hash.includes('noprint')) {
                    window.print();
                }
            }, 500);
        };
    </script>
</body>
</html>
