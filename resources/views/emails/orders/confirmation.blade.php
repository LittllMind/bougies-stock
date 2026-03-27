<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <style>
        body { font-family: Georgia, serif; background: #F5F5DC; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #D4AF37; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .panel { background: #F9F9F9; border-left: 4px solid #D4AF37; padding: 15px; margin: 15px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #D4AF37; color: white; text-decoration: none; border-radius: 4px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #F9F9F9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Les bougies de Séraphie</h1>
            <p>Confirmation de commande</p>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
            
            <p>Nous avons le plaisir de confirmer votre commande chez <strong>Les bougies de Séraphie</strong>.</p>
            
            <div class="panel">
                <h3>📦 Commande n° {{ $order->order_number }}</h3>
                <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                <strong>Statut :</strong> Payée ✓</p>
            </div>
            
            <h2>🕯️ Articles commandés</h2>
            <table>
                <thead>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix unit.</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->bougie->nom }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                        <td>{{ number_format($item->unit_price * $item->quantity, 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="panel">
                <p style="text-align: right; font-size: 18px;">
                    <strong>Total TTC : {{ number_format($order->total_amount, 2, ',', ' ') }} €</strong>
                </p>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('orders.show', $order) }}" class="btn">Voir ma commande</a>
            </p>
            
            <div class="panel">
                <h3>🐝 À propos de vos bougies</h3>
                <p>Toutes nos bougies sont fabriquées à la main avec de la <strong>cire d'abeille 100% naturelle</strong> de ruchers locaux. Sans parfum de synthèse, sans additifs chimiques — juste la pure chaleur dorée de la nature.</p>
            </div>
            
            <p>Des questions ? Contactez-nous à <strong>contact@bougiesraphie.com</strong>.</p>
            
            <p>Avec lumière et gratitude,<br><strong>Séraphie</strong> ✨</p>
        </div>
        
        <div class="footer">
            <p>Artisanat français • Cire d'abeille locale • Fabriqué à la main</p>
            <p>Les bougies de Séraphie, [Adresse de l'atelier]</p>
        </div>
    </div>
</body>
</html>
