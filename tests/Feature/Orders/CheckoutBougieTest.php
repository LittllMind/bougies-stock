<?php

namespace Tests\Feature\Orders;

use App\Models\Bougie;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutBougieTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        
        session()->flush();
        \Session::flush();
        
        $orderSessionKeys = ['pending_order_id', 'cart_total', 'shipping_address_id'];
        foreach ($orderSessionKeys as $key) {
            if (session()->has($key)) {
                session()->forget($key);
            }
        }

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->cartService = new CartService();
    }

    public function test_checkout_page_affiche_panier_avec_bougies()
    {
        $bougie = Bougie::factory()->create(['quantite' => 10, 'prix' => 22.50]);
        $this->cartService->addBougie($bougie->id, 2);

        $response = $this->get('/orders/create');

        $response->assertStatus(200);
        $response->assertSee($bougie->nom);
        $response->assertSee('Finaliser votre commande');
    }

    public function test_checkout_requiert_panier_non_vide()
    {
        $this->cartService->clear();
        $response = $this->get('/orders/create');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('error');
    }

    public function test_checkout_stocke_adresse_livraison()
    {
        $bougie = Bougie::factory()->create(['quantite' => 10]);
        $this->cartService->addBougie($bougie->id, 1);

        $response = $this->post('/orders', [
            'nom' => 'Jean Dupont',
            'email' => 'test@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $response->assertRedirect('/orders/payment');
        $this->assertTrue(session()->has('order_shipping'));
    }

    public function test_payment_page_affiche_recapitulatif()
    {
        $bougie = Bougie::factory()->create(['quantite' => 10, 'parfum' => "Parfum naturel de cire d'abeille", 'prix' => 22.50]);
        $this->cartService->addBougie($bougie->id, 2);
        session(['cart' => [['id' => $bougie->id, 'quantite' => 2]]]);

        $this->post('/orders', [
            'nom' => 'Jean Dupont',
            'email' => 'test@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $response = $this->followingRedirects()->get('/orders/payment');

        $response->assertStatus(200);
        $response->assertSee($bougie->nom);
        $response->assertSee('Paiement');
    }

    public function test_commande_est_creee_avec_bougies()
    {
        $bougie = Bougie::factory()->create(['quantite' => 10, 'prix' => 22.50]);
        $this->cartService->addBougie($bougie->id, 2);

        // POST /orders stocke l'adresse en session et redirige
        $response = $this->post('/orders', [
            'nom' => 'Jean Dupont',
            'email' => 'test@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $response->assertRedirect('/orders/payment');

        // La commande est créée lors de l'accès à la page de paiement
        $this->get('/orders/payment');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => 45.00,
            'statut' => 'pending'
        ]);

        $order = \App\Models\Order::where('user_id', $this->user->id)->first();
        $this->assertNotNull($order);
        $this->assertCount(1, $order->items);
        $this->assertEquals($bougie->id, $order->items->first()->bougie_id);
        $this->assertEquals(2, $order->items->first()->quantite);
    }

    /**
     * NOTE: Ce test est remplacé par StripeWebhookTest::test_webhook_decrements_stock_bougie
     * qui teste complètement le décrément du stock via le webhook Stripe.
     * Le webhook et décrément sont testés séparément pour éviter la complexité.
     */
    public function test_commande_decremente_stock_bougie_via_service()
    {
        $bougie = Bougie::factory()->create(['quantite' => 10]);
        $this->cartService->addBougie($bougie->id, 2);

        $this->post('/orders', [
            'nom' => 'Jean Dupont',
            'email' => 'test@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $this->get('/orders/payment');

        $order = \App\Models\Order::where('user_id', $this->user->id)->first();
        $this->assertNotNull($order);
        
        // Simuler le paiement via OrderService
        $orderService = app(\App\Services\OrderService::class);
        $result = $orderService->markOrderPaid($order);
        
        $this->assertTrue($result);
        
        // Vérifier que la commande est passée à payée
        $order->refresh();
        $this->assertEquals('paid', $order->statut);
        
        // Vérifier le décrément du stock
        $bougie->refresh();
        $this->assertEquals(8, $bougie->quantite);
    }
}
