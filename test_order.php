<?php

// Test rapide pour isoler l'erreur Checkout
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')-&gt;bootstrap();

// Simuler la création d'une commande
use App\Models\Order;

try {
    $order = Order::create([
        'numero_commande' =&gt; 'CMD-TEST-001',
        'user_id' =&gt; 1,
        'statut' =&gt; 'en_attente',
        'total' =&gt; 100.00,
        'nom' =&gt; 'Test',
        'prenom' =&gt; 'Test',
        'email' =&gt; 'test@test.com',
        'telephone' =&gt; '1234567890',
        'adresse' =&gt; '123 Rue Test',
        'code_postal' =&gt; '75000',
        'ville' =&gt; 'Paris',
        'shipping_nom' =&gt; 'Test',
        'shipping_prenom' =&gt; 'Test',
        'shipping_email' =&gt; 'test@test.com',
        'shipping_telephone' =&gt; '1234567890',
        'shipping_adresse' =&gt; '123 Rue Test',
        'shipping_code_postal' =&gt; '75000',
        'shipping_ville' =&gt; 'Paris',
        'shipping_pays' =&gt; 'FR',
        'billing_nom' =&gt; 'Test',
        'billing_prenom' =&gt; 'Test',
        'billing_email' =&gt; 'test@test.com',
        'billing_telephone' =&gt; '1234567890',
        'billing_adresse' =&gt; '123 Rue Test',
        'billing_code_postal' =&gt; '75000',
        'billing_ville' =&gt; 'Paris',
        'billing_pays' =&gt; 'FR',
    ]);
    echo "✅ Order créée: ID {$order->id}\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
