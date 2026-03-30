<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartVueTest extends TestCase
{
    use RefreshDatabase;

    public function test_vue_cart_initializes_correctly()
    {
        $bougie = Bougie::factory()->create([
            'nom' => 'Ganesh',
            'prix' => 45.00,
            'quantite' => 10
        ]);

        $user = User::factory()->create();
        
        // Ajouter au panier (utilise reference, pas bougie_id)
        $this->actingAs($user)
            ->postJson('/api/cart', [
                'reference' => $bougie->reference,
                'quantite' => 2
            ])
            ->assertOk();

        // Vérifier que la page panier charge avec Vue
        $response = $this->actingAs($user)->get('/cart');
        
        $response->assertStatus(200);
        $response->assertSee('cart-app');
        $response->assertSee('@vite/client', false);
    }

    public function test_cart_api_returns_items()
    {
        $bougie = Bougie::factory()->create(['prix' => 45.00]);
        $user = User::factory()->create();

        // Créer un panier en session manuellement
        session(['cart' => [
            ['reference' => $bougie->reference, 'quantite' => 2]
        ]]);

        $response = $this->actingAs($user)->getJson('/api/cart');
        
        $response->assertOk();
        $response->assertJsonStructure(['items', 'total', 'count']);
        $this->assertEquals(1, $response->json('count'));
    }

    public function test_empty_cart_shows_message()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/cart');
        
        $response->assertStatus(200);
        $response->assertSee('Votre panier est vide');
        $response->assertSee('@vite/client', false);
    }
}