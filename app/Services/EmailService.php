<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class EmailService
{
    /**
     * Envoie un email de confirmation de commande en HTML pur
     */
    public function sendOrderConfirmation(Order $order): void
    {
        $user = $order->user;
        $items = $order->items;
        
        $html = $this->buildOrderConfirmationHtml($order, $user, $items);
        
        Mail::html($html, function ($message) use ($user, $order) {
            $message->to($user->email, $user->name)
                ->subject("Confirmation de votre commande - Les bougies de Séraphie")
                ->from('contact@bougiesraphie.com', 'Les bougies de Séraphie');
        });
    }
    
    /**
     * Envoie un email de bienvenue
     */
    public function sendWelcomeEmail(User $user): void
    {
        $html = $this->buildWelcomeHtml($user);
        
        Mail::html($html, function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject("Bienvenue chez Les bougies de Séraphie")
                ->from('contact@bougiesraphie.com', 'Les bougies de Séraphie');
        });
    }
    
    /**
     * Construit le HTML de confirmation de commande
     */
    private function buildOrderConfirmationHtml(Order $order, $user, $items): string
    {
        $orderDate = $order->created_at->format('d/m/Y');
        $totalFormatted = number_format($order->total, 2, ',', ' ');
        $orderNumber = $order->numero_commande ?? $order->id;
        
        $userName = is_object($user) ? $user->name : ($order->full_name ?? 'Client');
        $userEmail = is_object($user) ? $user->email : $order->email;
        
        $itemsHtml = '';
        foreach ($items as $item) {
            $quantity = $item->quantite ?? 1;
            $unitPrice = $item->prix_unitaire ?? 0;
            $bougieName = $item->bougie->nom ?? 'Produit';
            
            $lineTotal = number_format($unitPrice * $quantity, 2, ',', ' ');
            $unitPriceFmt = number_format($unitPrice, 2, ',', ' ');
            $itemsHtml .= "<tr>
                <td style='padding:10px;border-bottom:1px solid #eee;'>{$bougieName}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:center;'>{$quantity}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;'>{$unitPriceFmt} €</td>
                <td style='padding:10px;border-bottom:1px solid #eee;'>{$lineTotal} €</td>
            </tr>";
        }
        
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Georgia, serif; background: #F5F5DC; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #D4AF37; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .panel { background: #F9F9F9; border-left: 4px solid #D4AF37; padding: 15px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #F9F9F9; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✨ Les bougies de Séraphie</h1>
            <p>Confirmation de commande</p>
        </div>
        <div class='content'>
            <p>Bonjour <strong>{$userName}</strong>,</p>
            <p>Nous confirmons votre commande chez <strong>Les bougies de Séraphie</strong>.</p>
            
            <div class='panel'>
                <h3>📦 Commande n° {$orderNumber}</h3>
                <p><strong>Date:</strong> {$orderDate}<br>
                <strong>Statut:</strong> Payée ✓</p>
            </div>
            
            <h2>🕯️ Articles commandés</h2>
            <table>
                <thead>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
            </table>
            
            <div class='panel'>
                <p style='text-align:right;font-size:18px;'>
                    <strong>Total TTC: {$totalFormatted} €</strong>
                </p>
            </div>
            
            <div class='panel'>
                <h3>🐝 À propos de vos bougies</h3>
                <p>Nos bougies sont fabriquées à la main avec de la <strong>cire d'abeille 100% naturelle</strong> de ruchers locaux. Sans parfum de synthèse, sans additifs chimiques.</p>
            </div>
            
            <p>Des questions ? <a href='mailto:contact@bougiesraphie.com'>contact@bougiesraphie.com</a></p>
            
            <p>Avec lumière et gratitude,<br><strong>Séraphie</strong> ✨</p>
        </div>
        
        <div class='footer'>
            <p>Artisanat français • Cire d'abeille locale • Fabriqué à la main</p>
        </div>
    </div>
</body>
</html>";
    }
    
    /**
     * Construit le HTML de bienvenue
     */
    private function buildWelcomeHtml(User $user): string
    {
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Georgia, serif; background: #F5F5DC; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #D4AF37; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; text-align: center; }
        .btn { display: inline-block; padding: 12px 24px; background: #D4AF37; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        .footer { padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✨ Bienvenue {$user->name}</h1>
        </div>
        <div class='content'>
            <p>Merci de rejoindre <strong>Les bougies de Séraphie</strong>.</p>
            <p>Découvrez nos bougies en cire d'abeille 100% naturelle.</p>
            <a href='https://localhost/catalogue' class='btn'>Voir le catalogue</a>
        </div>
        
        <div class='footer'>
            <p>Artisanat français • Cire d'abeille locale</p>
        </div>
    </div>
</body>
</html>";
    }
}
