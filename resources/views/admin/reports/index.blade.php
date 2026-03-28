<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports - Les bougies de Séraphie</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background: #FAFAFA;
            color: #333;
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #D4AF37;
        }
        .logo { font-size: 2rem; color: #D4AF37; margin-bottom: 10px; }
        .tagline { font-style: italic; color: #666; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
        
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #D4AF37;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 16px;
            background: #F5F5DC;
            border-radius: 6px;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #D4AF37;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            width: 100%;
        }
        
        .btn-primary {
            background: #D4AF37;
            color: #fff;
        }
        .btn-primary:hover { background: #B8941F; }
        
        .btn-secondary {
            background: #F5F5DC;
            color: #333;
            border: 1px solid #D4AF37;
        }
        .btn-secondary:hover { background: #EDE8D0; }
        
        .actions { display: flex; flex-direction: column; gap: 12px; }
        
        .alert-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .alert-banner {
            background: #fff3cd;
            border-left: 4px solid #D4AF37;
            padding: 16px 20px;
            border-radius: 0 6px 6px 0;
            margin-bottom: 20px;
        }
        
        .alert-banner h3 { 
            color: #856404; 
            margin-bottom: 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">🕯️ Les bougies de Séraphie</div>
            <p class="tagline">Rapports administratifs</p>
        </header>
        
        <div class="grid">
            <!-- Rapport Inventaire -->
            <div class="card">
                <h2 class="card-title">📦 Rapport d'Inventaire</h2>
                
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['total_bougies'] ?? 0 }}</div>
                        <div class="stat-label">Produits</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['total_unites'] ?? 0 }}</div>
                        <div class="stat-label">Unités stock</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($stats['total_valeur_stock'] ?? 0, 2, ',', ' ') }} €</div>
                        <div class="stat-label">Valeur stock</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: {{ ($stats['produits_en_alerte'] ?? 0) > 0 ? '#dc3545' : '#228B22' }}">
                            {{ $stats['produits_en_alerte'] ?? 0 }}
                        </div>
                        <div class="stat-label">Alertes</div>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="/admin/reports/inventory/pdf" class="btn btn-primary" target="_blank">
                        📄 Exporter PDF
                    </a>
                    <a href="/admin/reports/alerts/pdf" class="btn btn-secondary" target="_blank">
                        ⚠️ Exporter Alertes
                    </a>
                </div>
            </div>
            
            <!-- Rapport Financier -->
            <div class="card">
                <h2 class="card-title">💰 Rapport Financier</h2>
                
                <div class="actions" style="margin-top: 20px;">
                    <form action="/admin/reports/financial/pdf" method="GET" target="_blank">
                        <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">
                            Période du rapport
                        </label>
                        
                        <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                            <input type="date" name="start_date" value="{{ now()->subMonth()->format('Y-m-d') }}" 
                                   style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" 
                                   style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            📊 Générer Rapport Financier
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        @if(($stats['produits_en_alerte'] ?? 0) > 0)
        <div class="alert-section">
            <div class="alert-banner">
                <h3>⚠️ Alertes de Stock en Cours</h3>
                <p>{{ $stats['produits_en_alerte'] }} produits nécessitent votre attention.
                    <a href="/admin/reports/alerts/pdf" target="_blank" style="color: #856404; text-decoration: underline;">
                        Voir le rapport
                    </a>
                </p>
            </div>
        </div>
        @endif
        
        <footer class="footer">
            <p>Generated at {{ now()->format('d/m/Y H:i') }} | Les bougies de Séraphie</p>
        </footer>
    
    </div>
</body>
</html>