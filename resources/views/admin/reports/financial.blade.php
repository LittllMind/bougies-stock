<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Financier - Les bougies de Séraphie</title>
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
        .period { 
            color: #999; 
            font-size: 1rem; 
            margin-top: 8px;
            font-style: italic;
        }
        
        .kpi-grid {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
        }
        .kpi-card {
            flex: 1;
            background: #F5F5DC;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-top: 4px solid #D4AF37;
        }
        .kpi-value { font-size: 2rem; font-weight: bold; color: #D4AF37; }
        .kpi-label { font-size: 0.9rem; color: #666; margin-top: 8px; }
        
        .section { margin-bottom: 30px; }
        .section-title {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #D4AF37;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        
        .collection-bar {
            background: #F5F5DC;
            height: 8px;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }
        .collection-fill {
            background: #D4AF37;
            height: 100%;
            border-radius: 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 0.85rem;
        }
        
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
            <div class="report-title">💰 Rapport Financier</div>
            <div class="period">
                Période : {{ $stats['periode_start']->format('d/m/Y') }} au {{ $stats['periode_end']->format('d/m/Y') }}
            </div>
        </header>
        
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($stats['total_revenus'] ?? 0, 0, ',', ' ') }} €</div>
                <div class="kpi-label">Chiffre d'affaires</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $stats['nombre_commandes'] ?? 0 }}</div>
                <div class="kpi-label">Commandes</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($stats['panier_moyen'] ?? 0, 2, ',', ' ') }} €</div>
                <div class="kpi-label">Panier moyen</div>
            </div>
        </div>
        
        @if(count($sales_by_collection) > 0)
        <div class="section">
            <h2 class="section-title">Ventes par Collection</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Collection</th>
                        <th class="text-center">Unités vendues</th>
                        <th class="text-right">Chiffre d'affaires</th>
                        <th>Répartition</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxCa = collect($sales_by_collection)->max('ca'); @endphp
                    @foreach($sales_by_collection as $collection)
                    <tr>
                        <td><strong>{{ $collection['nom'] }}</strong></td>
                        <td class="text-center">{{ $collection['unites'] }}</td>
                        <td class="text-right">{{ number_format($collection['ca'], 2, ',', ' ') }} €</td>
                        <td>
                            @php $percentage = $maxCa > 0 ? ($collection['ca'] / $maxCa) * 100 : 0; @endphp
                            <div class="collection-bar">
                                <div class="collection-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span style="font-size: 0.8rem; color: #666;">{{ round($percentage) }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if(count($top_products) > 0)
        <div class="section">
            <h2 class="section-title">Top 5 des Produits</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Collection</th>
                        <th class="text-center">Vendus</th>
                        <th class="text-right">CA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_products as $product)
                    <tr>
                        <td>{{ $product['bougie']->nom }}</td>
                        <td>{{ $product['bougie']->collection ?: 'Standard' }}</td>
                        <td class="text-center">{{ $product['unites'] }}</td>
                        <td class="text-right">{{ number_format($product['ca'], 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <footer class="footer">
            <p>Les bougies de Séraphie — L'art de la lumière pure 🐝</p>
            <p>Généré le {{ $generated_at->format('d/m/Y à H:i') }}</p>
        </footer>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.focus();
                window.print();
            }, 500);
        };
    </script>
</body>
</html>