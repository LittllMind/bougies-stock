<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: API crée commande depuis panier
     */
    public function test_api_cree_commande_depuis_panier()
    {
        $this->artisan('db:seed', ['--class' => 'BougieSeeder']);
        
        $bougie = Bougie::first();
        $response = $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 2
        ]);

        $response->assertStatus(200);

        $orderData = [
            'nom_client' => 'Jean Test',
            'email' => 'jean@test.com',
            'adresse' => '12 Rue des Lilas',
            'ville' => 'Paris',
            'code_postal' => '75001',
            'telephone' => '0123456789'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'order_id',
                'reference',
                'message'
            ]);

        $this->assertDatabaseHas('orders', [
            'nom_client' => 'Jean Test',
            'email' => 'jean@test.com',
            'statut' => 'pending'
        ]);

        $this->assertDatabaseHas('order_items', [
            'bougie_id' => $bougie->id,
            'quantite' => 2,
            'prix_unitaire' => $bougie->prix
        ]);
    }

    /**
     * Test: Validation champs obligatoires pour commande
     */
    public function test_validation_champs_obligatoires_pour_commande()
    {
        $this->artisan('db:seed', ['--class' => 'BougieSeeder']);
        
        $bougie = Bougie::first();
        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 1
        ]);

        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom_client', 'email', 'adresse', 'ville', 'code_postal']);
    }

    /**
     * Test: Stock diminué après commande
     */
    public function test_stock_diminue_apres_commande()
    {
        $this->artisan('db:seed', ['--class' => 'BougieSeeder']);
        
        $bougie = Bougie::first();
        $quantiteInitiale = $bougie->quantite;
        
        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 2
        ]);

        $this->postJson('/api/orders', [
            'nom_client' => 'Test',
            'email' => 'test@test.com',
            'adresse' => '123 Test',
            'ville' => 'Paris',
            'code_postal' => '75001'
        ]);

        $bougie->refresh();
        $this->assertEquals($quantiteInitiale - 2, $bougie->quantite);
    }

    /**
     * Test: Alerte stock créée si seuil atteint
     */
    public function test_alerte_stock_creee_si_seuil_atteint()
    {
        $bougie = Bougie::factory()->create([
            'quantite' => 7,
            'seuil_alerte' => 5
        ]);

        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 3
        ]);

        $this->postJson('/api/orders', [
            'nom_client' => 'Test',
            'email' => 'test@test.com',
            'adresse' => '123 Test',
            'ville' => 'Paris',
            'code_postal' => '75001'
        ]);

        // Correction: stock_alerts utilise stockable_type et stockable_id
        $this->assertDatabaseHas('stock_alerts', [
            'stockable_type' => 'App\Models\Bougie',
            'stockable_id' => $bougie->id,
            'statut' => 'actif'
        ]);
    }

    /**
     * Test: Impossible de commander si panier vide
     */
    public function test_impossible_commander_si_panier_vide()
    {
        $response = $this->postJson('/api/orders', [
            'nom_client' => 'Test',
            'email' => 'test@test.com',
            'adresse' => '123 Test',
            'ville' => 'Paris',
            'code_postal' => '75001'
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Le panier est vide']);
    }

    /**
     * Test: Impossible commander si stock insuffisant
     */
    public function test_impossible_commander_si_stock_insuffisant()
    {
        $bougie = Bougie::factory()->create([
            'quantite' => 2
        ]);

        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 5
        ]);

        $response = $this->postJson('/api/orders', [
            'nom_client' => 'Test',
            'email' => 'test@test.com',
            'adresse' => '123 Test',
            'ville' => 'Paris',
            'code_postal' => '75001'
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Stock insuffisant pour certains articles']);
    }

    /**
     * Test: Page checkout affiche Vue.js
     */
    public function test_page_checkout_affiche_vuejs()
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200)
            ->assertViewIs('orders.checkout')
            ->assertSee('checkoutApp');
    }

    /**
     * Test: Page confirmation affiche détails commande
     */
    public function test_page_confirmation_affiche_details_commande()
    {
        $this->artisan('db:seed', ['--class' => 'BougieSeeder']);
        
        $bougie = Bougie::first();
        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 2
        ]);

        $response = $this->postJson('/api/orders', [
            'nom_client' => 'Marie Test',
            'email' => 'marie@test.com',
            'adresse' => '45 Avenue des Fleurs',
            'ville' => 'Lyon',
            'code_postal' => '69001'
        ]);

        $orderRef = $response->json('reference');

        $response = $this->get("/confirmation/{$orderRef}");

        $response->assertStatus(200)
            ->assertViewIs('orders.confirmation')
            ->assertSee('Marie Test')
            ->assertSee($orderRef);
    }

    /**
     * Test: API retourne référence unique
     */
    public function test_api_retourne_reference_unique()
    {
        $this->artisan('db:seed', ['--class' => 'BougieSeeder']);
        
        $bougie = Bougie::first();
        $this->postJson('/api/cart', [
            'reference' => $bougie->reference,
            'quantite' => 1
        ]);

        $response = $this->postJson('/api/orders', [
            'nom_client' => 'Test',
            'email' => 'test@test.com',
            'adresse' => '123 Test',
            'ville' => 'Paris',
            'code_postal' => '75001'
        ]);

        $reference = $response->json('reference');
        $this->assertNotNull($reference);
        $this->assertStringStartsWith('BOG-', $reference);
    }
}