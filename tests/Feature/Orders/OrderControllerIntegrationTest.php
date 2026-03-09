<?php

namespace Tests\Feature\Orders;

use Tests\TestCase;
use App\Models\Vinyle;
use App\Models\Fond;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class OrderControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
    }

    // ============================================
    // TESTS CRÉATION COMMANDE
    // ============================================

    /**
     * Test @data order-create-guest
     * Un invité peut accéder au formulaire de commande avec panier vide
     */
    public function test_guest_can_access_order_create_form(): void
    {
        $response = $this->get(route('orders.create'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Votre panier est vide. Ajoutez des vinyles avant de commander.');
    }

    /**
     * Test @data order-create-guest-with-cart
     * Un invité peut accéder au formulaire avec des articles dans le panier
     */
    public function test_guest_can_access_order_form_with_cart_items(): void
    {
        $vinyle = Vinyle::factory()->create(['prix' => 25.00, 'quantite' => 10]);
        
        // Ajouter au panier via session/cookie
        $this->cartService->addVinyle($vinyle->id, 2);

        $response = $this->get(route('orders.create'));

        $response->assertOk();
        $response->assertViewIs('orders.create');
        $response->assertViewHas('cart');
    }

    /**
     * Test @data order-create-auth
     * Un utilisateur connecté voit ses adresses sauvegardées
     */
    public function test_authenticated_user_sees_saved_addresses(): void
    {
        $user = $this->clientUser();
        $vinyle = Vinyle::factory()->create(['prix' => 25.00, 'quantite' => 10]);
        
        $this->actingAs($user);
        $this->cartService->addVinyle($vinyle->id, 2);
        
        // Créer une adresse pour l'utilisateur
        $address = $user->addresses()->create([
            'label' => 'Maison',
            'nom' => 'Dupont',
            'email' => 'test@test.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'is_default' => true,
        ]);

        $response = $this->get(route('orders.create'));

        $response->assertOk();
        $response->assertViewHas('addresses');
    }

    // ============================================
    // TESTS SOUMISSION COMMANDE
    // ============================================

    /**
     * Test @data order-store-validation
     * Validation des champs obligatoires
     */
    public function test_order_store_requires_all_shipping_fields(): void
    {
        $vinyle = Vinyle::factory()->create(['quantite' => 10]);
        $this->cartService->addVinyle($vinyle->id, 1);

        $response = $this->post(route('orders.store'), []);

        $response->assertSessionHasErrors([
            'nom', 'email', 'telephone', 'adresse', 
            'code_postal', 'ville', 'pays'
        ]);
    }

    /**
     * Test @data order-store-success
     * Création réussie d'une commande avec adresse de livraison
     */
    public function test_order_store_creates_order_successfully(): void
    {
        $vinyle = Vinyle::factory()->create([
            'nom' => 'Test Album',
            'prix' => 25.00,
            'quantite' => 10
        ]);
        $this->cartService->addVinyle($vinyle->id, 2);

        $orderData = [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
            'instructions' => 'Laissez devant la porte',
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertRedirect(route('orders.payment'));
        $response->assertSessionHas('order_shipping');
        
        $this->assertDatabaseHas('orders', [
            'email' => 'jean@example.com',
            'statut' => 'en_attente',
        ]);
    }

    /**
     * Test @data order-store-different-billing
     * Commande avec adresse de facturation différente
     */
    public function test_order_allows_different_billing_address(): void
    {
        $vinyle = Vinyle::factory()->create(['prix' => 25.00, 'quantite' => 10]);
        $this->cartService->addVinyle($vinyle->id, 1);

        $orderData = [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Livraison',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
            'use_same_address' => '0',
            'facturation_nom' => 'Marie Dupont',
            'facturation_email' => 'marie@example.com',
            'facturation_telephone' => '0687654321',
            'facturation_adresse' => '456 Rue Facturation',
            'facturation_code_postal' => '69001',
            'facturation_ville' => 'Lyon',
            'facturation_pays' => 'FR',
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertRedirect(route('orders.payment'));
        
        $billing = Session::get('order_billing');
        $this->assertEquals('Marie Dupont', $billing['nom']);
        $this->assertEquals('456 Rue Facturation', $billing['adresse']);
    }

    // ============================================
    // TESTS PAIEMENT ET COMMANDE
    // ============================================

    /**
     * Test @data order-payment-empty-cart
     * Redirection si panier vide
     */
    public function test_payment_redirects_if_cart_empty(): void
    {
        $response = $this->get(route('orders.payment'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test @data order-payment-no-shipping
     * Redirection si pas d'adresse de livraison
     */
    public function test_payment_redirects_if_no_shipping_info(): void
    {
        $vinyle = Vinyle::factory()->create(['prix' => 25.00, 'quantite' => 10]);
        $this->cartService->addVinyle($vinyle->id, 1);

        $response = $this->get(route('orders.payment'));

        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHas('error');
    }

    /**
     * Test @data order-payment-creates-order
     * La page de paiement crée la commande
     */
    public function test_payment_creates_pending_order(): void
    {
        $vinyle = Vinyle::factory()->create([
            'nom' => 'Album Test',
            'prix' => 30.00,
            'quantite' => 5
        ]);
        $this->cartService->addVinyle($vinyle->id, 2);

        // Simuler les données de session
        Session::put('order_shipping', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->get(route('orders.payment'));

        $response->assertOk();
        $response->assertViewHas('order');
        
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('en_attente', $order->statut);
        $this->assertEquals(60.00, $order->total); // 30.00 * 2
    }

    /**
     * Test @data order-payment-reuses-existing
     * Réutilise une commande en attente existante
     */
    public function test_payment_reuses_existing_pending_order(): void
    {
        $user = $this->clientUser();
        $vinyle = Vinyle::factory()->create(['prix' => 25.00]);
        
        $this->actingAs($user);
        $this->cartService->addVinyle($vinyle->id, 1);

        // Créer une commande en attente
        $existingOrder = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 25.00,
        ]);

        Session::put('pending_order_id', $existingOrder->id);
        Session::put('order_shipping', [
            'nom' => 'Jean',
            'email' => 'test@test.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->get(route('orders.payment'));

        $response->assertOk();
        $this->assertDatabaseCount('orders', 1); // Pas de doublon
    }

    // ============================================
    // TESTS MES COMMANDES
    // ============================================

    /**
     * Test @data my-orders-auth
     * Utilisateur connecté voit ses commandes
     */
    public function test_authenticated_user_sees_own_orders(): void
    {
        $user = $this->clientUser();
        $orders = Order::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('orders.my'));

        $response->assertOk();
        $response->assertViewIs('orders.my');
        $response->assertViewHas('orders');
    }

    /**
     * Test @data my-orders-empty
     * Message si aucune commande
     */
    public function test_my_orders_shows_empty_state(): void
    {
        $user = $this->clientUser();

        $response = $this->actingAs($user)->get(route('orders.my'));

        $response->assertOk();
        // Vérifie que la vue est rendue même sans commandes
    }

    /**
     * Test @data my-orders-paginate
     * Pagination des commandes
     */
    public function test_my_orders_is_paginated(): void
    {
        $user = $this->clientUser();
        Order::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('orders.my'));

        $response->assertOk();
        $response->assertViewHas('orders', function ($orders) {
            return $orders->count() <= 10; // Pagination par défaut
        });
    }

    // ============================================
    // TESTS INTÉGRATION STOCK
    // ============================================

    /**
     * Test @data order-stock-check
     * Vérification du stock disponible
     */
    public function test_cart_checks_stock_before_order(): void
    {
        $vinyle = Vinyle::factory()->create(['quantite' => 2, 'prix' => 25.00]);
        
        // Essayer d'ajouter plus que le stock disponible
        $this->cartService->addVinyle($vinyle->id, 5);
        
        $stockErrors = $this->cartService->checkStock();
        
        $this->assertNotEmpty($stockErrors);
    }

    /**
     * Test @data order-with-fond
     * Commande avec fond sélectionné
     */
    public function test_order_can_include_fond_selection(): void
    {
        $vinyle = Vinyle::factory()->create(['prix' => 25.00, 'quantite' => 10]);
        Fond::factory()->create(['type' => 'miroir', 'quantite' => 5]);
        
        // Ajouter au panier avec fond miroir
        $this->cartService->addVinyle($vinyle->id, 1, 'miroir');

        $cart = $this->cartService->getCart();
        $item = $cart->items->first();
        
        $this->assertNotNull($item->fond);
        $this->assertEquals('miroir', $item->fond->type);
    }

    // ============================================
    // TESTS ANNULATION ET SUCCÈS
    // ============================================

    /**
     * Test @data order-success-page
     * Page de succès accessible
     */
    public function test_success_page_is_accessible(): void
    {
        $response = $this->get(route('orders.success'));

        $response->assertOk();
        $response->assertViewIs('orders.success');
    }

    /**
     * Test @data order-cancel-page
     * Page d'annulation avec message
     */
    public function test_cancel_page_shows_error_message(): void
    {
        $response = $this->get(route('orders.cancel'));

        $response->assertOk();
        $response->assertViewIs('orders.cancel');
        $response->assertSessionHas('error');
    }

    // ============================================
    // TESTS FLOW COMPLET
    // ============================================

    /**
     * Test @data order-flow-complete
     * Flow complet : ajout panier → commande → paiement
     */
    public function test_complete_order_flow(): void
    {
        $vinyle = Vinyle::factory()->create([
            'nom' => 'Dark Side of the Moon',
            'prix' => 35.00,
            'quantite' => 10,
        ]);

        // Étape 1 : Ajouter au panier
        $this->cartService->addVinyle($vinyle->id, 2);
        
        // Étape 2 : Accéder au formulaire de commande
        $response = $this->get(route('orders.create'));
        $response->assertOk();

        // Étape 3 : Soumettre les infos de livraison
        $orderData = [
            'nom' => 'Jean Dupont',
            'email' => 'jean@test.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Musique',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ];

        $response = $this->post(route('orders.store'), $orderData);
        $response->assertRedirect(route('orders.payment'));

        // Étape 4 : Page de paiement
        $response = $this->get(route('orders.payment'));
        $response->assertOk();
        $response->assertViewHas('order');

        // Vérifications finales
        $this->assertDatabaseHas('orders', [
            'email' => 'jean@test.com',
            'statut' => 'en_attente',
            'total' => 70.00, // 35.00 * 2
        ]);

        $order = Order::first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'quantite' => 2,
            'prix_unitaire' => 35.00,
        ]);
    }

    /**
     * Test @data order-flow-with-auth
     * Flow complet avec utilisateur authentifié
     */
    public function test_complete_order_flow_with_authenticated_user(): void
    {
        $user = $this->clientUser();
        $this->actingAs($user);

        $vinyle = Vinyle::factory()->create([
            'prix' => 40.00,
            'quantite' => 5,
        ]);

        // Flow complet
        $this->cartService->addVinyle($vinyle->id, 1);
        
        $orderData = [
            'nom' => $user->name,
            'email' => $user->email,
            'telephone' => '0612345678',
            'adresse' => '123 Rue Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
            'save_address' => true,
            'address_label' => 'Maison',
        ];

        $this->post(route('orders.store'), $orderData);
        $this->get(route('orders.payment'));

        // Vérifier que l'adresse est sauvegardée
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Maison',
        ]);

        // Vérifier la commande est liée à l'utilisateur
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}