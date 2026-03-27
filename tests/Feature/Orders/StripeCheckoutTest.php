<?php

namespace Tests\Feature\Orders;

use App\Models\Bougie;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Checkout\Session;
use Tests\TestCase;

/**
 * Tests du flux de paiement Stripe Checkout
 * Flux: Panier → Commande (orders.payment) → Paiement (payment.checkout)
 */
class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;
    protected User $user;
    protected Bougie $bougie;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cartService = app(CartService::class);
        
        // Créer une bougie pour les tests
        $this->bougie = Bougie::factory()->create([
            'nom' => 'Ganesh',
            'reference' => 'BOUG-001',
            'prix' => 45.00,
            'quantite' => 10,
            'collection' => 'Spirit',
        ]);
        
        // Créer un utilisateur
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Jean Test',
        ]);
    }

    /**
     * Crée un panier avec une bougie et retourne le panier
     */
    protected function addBougieToCart(int $quantite = 1): Cart
    {
        $this->actingAs($this->user);
        
        $cart = $this->cartService->getCart();
        CartItem::create([
            'cart_id' => $cart->id,
            'bougie_id' => $this->bougie->id,
            'vinyle_id' => null, // Legacy nullable
            'quantite' => $quantite,
            'prix_unitaire' => $this->bougie->prix,
        ]);
        
        return $cart;
    }

    /**
     * Crée la commande depuis le panier en appelant orders.payment
     */
    protected function createOrderFromCart(): Order
    {
        $cart = $this->addBougieToCart(2);
        
        // Stocker les infos de livraison en session
        session([
            'order_shipping' => [
                'nom' => 'Jean Test',
                'email' => 'test@example.com',
                'telephone' => '0612345678',
                'adresse' => '123 Rue Test',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ],
            'order_billing' => [
                'nom' => 'Jean Test',
                'email' => 'test@example.com',
                'telephone' => '0612345678',
                'adresse' => '123 Rue Test',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ],
        ]);
        
        // Appeler la page payment qui crée la commande
        $response = $this->actingAs($this->user)
            ->get(route('orders.payment'));
            
        // Récupérer la commande créée
        $order = Order::where('user_id', $this->user->id)
            ->where('statut', 'en_attente')
            ->first();
            
        $this->assertNotNull($order, 'La commande aurait dû être créée');
        
        return $order;
    }

    /**
     * Mock Stripe Checkout Session
     */
    protected function mockStripeSession(array $data = []): \stdClass
    {
        $mockSession = new \stdClass();
        $mockSession->id = $data['id'] ?? 'cs_test_' . uniqid();
        $mockSession->url = $data['url'] ?? 'https://checkout.stripe.test/session/' . time();
        $mockSession->payment_intent = $data['payment_intent'] ?? 'pi_test_' . uniqid();
        
        // Mock la classe Session de Stripe
        $mock = Mockery::mock('alias:' . Session::class);
        $mock->shouldReceive('create')->andReturn($mockSession);
        
        return $mockSession;
    }

    /** @test */
    public function test_checkout_redirect_vers_stripe_avec_commande_valide(): void
    {
        $order = $this->createOrderFromCart();
        
        // Mock Stripe Session
        $mockSession = $this->mockStripeSession([
            'id' => 'cs_test_123',
            'url' => 'https://checkout.stripe.test/session/123',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        // Le controller redirige vers Stripe, pas JSON
        $response->assertRedirect($mockSession->url);
    }

    /** @test */
    public function test_checkout_requiert_order_id(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('payment.checkout'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id']);
    }

    /** @test */
    public function test_checkout_requiert_authentification(): void
    {
        $response = $this->postJson(route('payment.checkout'), [
            'order_id' => 999,
        ]);
        
        $response->assertStatus(401);
    }

    /** @test */
    public function test_checkout_refuse_commande_dun_autre_utilisateur(): void
    {
        // Créer une commande pour l'utilisateur actuel
        $order = $this->createOrderFromCart();
        
        // Créer un autre utilisateur et essayer d'accéder à la commande
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($otherUser)
            ->postJson(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_checkout_cree_enregistrement_paiement_en_attente(): void
    {
        $order = $this->createOrderFromCart();
        
        $this->mockStripeSession([
            'id' => 'cs_test_payment_record',
            'payment_intent' => 'pi_test_record',
        ]);

        $this->actingAs($this->user)
            ->post(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        // Vérifier que le paiement est créé en base
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'stripe_session_id' => 'cs_test_payment_record',
            'status' => 'pending',
            'amount' => 90.00, // 45 * 2
            'currency' => 'eur',
        ]);
    }

    /** @test */
    public function test_checkout_calcule_montant_total_correctement(): void
    {
        // Panier avec quantité différente
        $this->addBougieToCart(3); // Re-crée panier avec 3
        
        session([
            'order_shipping' => [
                'nom' => 'Test',
                'email' => 't@test.com',
                'telephone' => '0612345678',
                'adresse' => '123 Rue',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ],
            'order_billing' => [
                'nom' => 'Test',
                'email' => 't@test.com',
                'telephone' => '0612345678',
                'adresse' => '123 Rue',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ],
        ]);
        
        $this->get(route('orders.payment'));
        
        $order = Order::where('user_id', $this->user->id)
            ->where('statut', 'en_attente')
            ->first();

        // Vérifier que l'order total est correct : 45 * 3 = 135
        $this->assertEquals(135.00, $order->total);
        
        $this->mockStripeSession();
        
        $response = $this->actingAs($this->user)
            ->post(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        $response->assertRedirect();
    }

    /** @test */
    public function test_checkout_inclut_metadata_order_et_user(): void
    {
        $order = $this->createOrderFromCart();
        
        $capturedArgs = null;
        $mock = Mockery::mock('alias:' . Session::class);
        $mock->shouldReceive('create')->once()->andReturnUsing(function ($args) use (&$capturedArgs) {
            $capturedArgs = $args;
            $mockSession = new \stdClass();
            $mockSession->id = 'cs_test_' . time();
            $mockSession->url = 'https://checkout.stripe.test/session/test';
            return $mockSession;
        });

        $this->actingAs($this->user)
            ->post(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        $this->assertNotNull($capturedArgs);
        $this->assertArrayHasKey('metadata', $capturedArgs);
        $this->assertEquals($order->id, $capturedArgs['metadata']['order_id']);
        $this->assertEquals($this->user->id, $capturedArgs['metadata']['user_id']);
    }

    /** @test */
    public function test_checkout_utilise_mode_payment_et_currency_eur(): void
    {
        $order = $this->createOrderFromCart();
        
        $capturedArgs = null;
        $mock = Mockery::mock('alias:' . Session::class);
        $mock->shouldReceive('create')->once()->andReturnUsing(function ($args) use (&$capturedArgs) {
            $capturedArgs = $args;
            $mockSession = new \stdClass();
            $mockSession->id = 'cs_test_' . time();
            $mockSession->url = 'https://checkout.stripe.test/session/test';
            return $mockSession;
        });

        $this->actingAs($this->user)
            ->post(route('payment.checkout'), [
                'order_id' => $order->id,
            ]);

        $this->assertNotNull($capturedArgs);
        $this->assertEquals('payment', $capturedArgs['mode']);
        $this->assertStringContainsString('eur', $capturedArgs['line_items'][0]['price_data']['currency']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
