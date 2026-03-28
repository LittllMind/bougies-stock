<?php
// tests/Feature/DebugOrderTest.php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_api_order_endpoint()
    {
        // Créer une bougie
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-TEST-001',
            'nom' => 'Test Bougie',
            'parfum' => 'Vanille',
            'quantite' => 10,
            'prix' => 25.00,
        ]);

        // Ajouter au panier session
        session(['cart' => [
            ['reference' => 'BOUG-TEST-001', 'quantite' => 2]
        ]]);

        // Appeler l'API
        $orderData = [
            'nom_client' => 'Jean Test',
            'email' => 'jean@test.com',
            'adresse' => '12 Rue des Lilas',
            'ville' => 'Paris',
            'code_postal' => '75001',
            'telephone' => '0123456789',
        ];

        try {
            $response = $this->postJson('/api/orders', $orderData);
            
            // Afficher la réponse
            $this->artisan('inspire'); // Pour voir output
            dump('Status: ' . $response->getStatusCode());
            dump('Response: ', $response->json());
            
            // Si erreur 500, le test va échouer
            $this->assertEquals(201, $response->getStatusCode());
            
        } catch (\Exception $e) {
            dump('ERREUR API: ' . $e->getMessage());
            if ($e->getPrevious()) {
                dump('Cause: ' . $e->getPrevious()->getMessage());
            }
            $this->fail($e->getMessage());
        }
    }
}