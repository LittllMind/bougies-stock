<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Bougie;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: API ajoute article au panier en session
     */
    public function test_api_ajoute_article_au_panier_session()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        $response = $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Article ajouté au panier',
                 ]);

        // Vérifier que le panier est en session
        $cart = session('cart');
        $this->assertNotNull($cart);
        $this->assertCount(1, $cart);
        $this->assertEquals('BOUG-001', $cart[0]['reference']);
        $this->assertEquals(2, $cart[0]['quantite']);
    }

    /**
     * Test: Panier récupéré par l'API
     */
    public function test_api_retourne_panier_actuel()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        // Ajouter au panier
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 2,
        ]);

        // Récupérer le panier
        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'items')
                 ->assertJsonPath('items.0.reference', 'BOUG-001')
                 ->assertJsonPath('items.0.quantite', 2);
        
        // Vérifier les valeurs numériques
        $data = $response->json();
        $this->assertEquals(25.0, $data['items'][0]['prix_unitaire']);
        $this->assertEquals(50.0, $data['items'][0]['sous_total']);
        $this->assertEquals(50.0, $data['total']);
    }

    /**
     * Test: Quantité modifiable dans le panier via API
     */
    public function test_api_modifie_quantite_dans_panier()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        // Ajouter au panier
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 2,
        ]);

        // Modifier la quantité
        $response = $this->patchJson('/api/cart/BOUG-001', [
            'quantite' => 5,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Quantité mise à jour',
                 ]);

        // Vérifier la mise à jour
        $cart = session('cart');
        $this->assertEquals(5, $cart[0]['quantite']);
    }

    /**
     * Test: Suppression article du panier via API
     */
    public function test_api_supprime_article_du_panier()
    {
        $bougie1 = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        $bougie2 = Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'nom' => 'Bougie Lavande',
            'prix' => 30.00,
            'quantite' => 5,
        ]);

        // Ajouter deux articles
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 1,
        ]);
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-002',
            'quantite' => 1,
        ]);

        // Supprimer le premier article
        $response = $this->deleteJson('/api/cart/BOUG-001');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Article supprimé du panier',
                 ]);

        // Vérifier qu'il reste un seul article
        $cart = session('cart');
        $this->assertCount(1, $cart);
        $this->assertEquals('BOUG-002', $cart[0]['reference']);
    }

    /**
     * Test: Calcul total panier correct
     */
    public function test_calcul_total_panier_correct()
    {
        $bougie1 = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        $bougie2 = Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'nom' => 'Bougie Lavande',
            'prix' => 30.00,
            'quantite' => 5,
        ]);

        // Ajouter au panier
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 2,  // 50€
        ]);
        $this->postJson('/api/cart', [
            'reference' => 'BOUG-002',
            'quantite' => 3,  // 90€
        ]);

        // Récupérer le panier
        $response = $this->getJson('/api/cart');

        // Total = 50 + 90 = 140€
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'items');
        
        // Vérifier le total (accepte 140 ou 140.0)
        $data = $response->json();
        $this->assertEquals(140, $data['total']);
        $this->assertEquals(50, $data['items'][0]['sous_total']);
        $this->assertEquals(90, $data['items'][1]['sous_total']);
    }

    /**
     * Test: Validation stock disponible lors de l'ajout
     */
    public function test_refuse_ajout_si_quantite_superieur_stock()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'prix' => 25.00,
            'quantite' => 5,  // Stock limité
        ]);

        $response = $this->postJson('/api/cart', [
            'reference' => 'BOUG-001',
            'quantite' => 10,  // Demande plus que le stock
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['quantite']);
    }

    /**
     * Test: Page panier affiche Vue.js
     */
    public function test_page_panier_affiche_vuejs()
    {
        $response = $this->get('/cart');

        $response->assertStatus(200)
                 ->assertViewIs('cart.index')
                 ->assertSee('cart-app', false)
                 ->assertSee('v-if', false);
    }

    /**
     * Test: Panier vide retourne réponse appropriée
     */
    public function test_panier_vide_retourne_reponse_appropriee()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJson([
                     'items' => [],
                     'total' => 0,
                     'count' => 0,
                     'message' => 'Votre panier est vide',
                 ]);
    }
}