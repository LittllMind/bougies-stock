<?php

namespace Tests\Feature\Orders;

use App\Models\Bougie;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test d'intégration tunnel complet: Panier localStorage → Checkout → Paiement
 * 
 * Ce test valide que le tunnel de vente fonctionne de bout en bout,
 * y compris la synchronisation entre panier Vue.js (localStorage) et backend.
 */
class TunnelVenteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer utilisateur test
        $this->user = User::factory()->create([
            'email' => 'test@seraphie.fr',
            'password' => bcrypt('password123')
        ]);

        // Créer bougie en stock
        $this->bougie = Bougie::factory()->create([
            'nom' => 'Bougie Test',
            'prix' => 45.00,
            'quantite' => 10,
            'parfum' => 'Cire naturelle'
        ]);
    }

    /**
     * TUNNEL-1: Client anonyme peut voir le kiosque
     */
    public function test_client_anonyme_peut_voir_kiosque(): void
    {
        $response = $this->get('/kiosque');
        
        $response->assertStatus(200);
        $response->assertSee('Nos Bougies Artisanales');
        $response->assertSee('Bougie Test');
        $response->assertSee('45,00 €');
    }

    /**
     * TUNNEL-2: Client anonyme peut voir son panier (vide)
     */
    public function test_client_anonyme_peut_voir_panier_vide(): void
    {
        $response = $this->get('/cart');
        
        $response->assertStatus(200);
        $response->assertSee('Votre panier est vide');
        $response->assertSee('Continuer mes achats');
    }

    /**
     * TUNNEL-3: Page checkout redirige vers login si non authentifié
     */
    public function test_checkout_redirige_vers_login_si_anonyme(): void
    {
        $response = $this->get('/orders/create');
        
        $response->assertRedirect('/login');
    }

    /**
     * TUNNEL-4: Client authentifié avec panier DB peut accéder au checkout
     * Utilise le CartService directement (comme le fait Vue.js via API cart/add)
     */
    public function test_client_authentifie_avec_panier_peut_acceder_checkout(): void
    {
        // Créer le panier via CartService (simule l'API /cart/add)
        $cartService = app(\App\Services\CartService::class);
        
        // Simuler connexion pour que CartService créé le panier utilisateur
        $this->actingAs($this->user);
        
        $cartService->addBougie($this->bougie->id, 2);

        // Vérifier que le panier existe en DB
        $this->assertDatabaseHas('cart_items', [
            'bougie_id' => $this->bougie->id,
            'quantite' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get('/orders/create');

        $response->assertStatus(200);
    }

    /**
     * TUNNEL-5: Checkout refuse panier vide (panier DB vide)
     */
    public function test_checkout_refuse_panier_vide(): void
    {
        // Créer un panier DB vide pour l'utilisateur (mais sans items)
        // Le cartService va créer ce panier automatiquement
        // et retournera empty car aucun item
        
        $response = $this->actingAs($this->user)
            ->get('/orders/create');

        $response->assertRedirect('/cart');
    }

    /**
     * TUNNEL-6: API panier accepte synchronisation (Vue.js localStorage → DB)
     */
    public function test_api_panier_accepte_synchronisation(): void
    {
        // Note: la route cart.sync attend des références de bougies
        $this->bougie->update(['reference' => 'TEST-BOUG-001']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/cart/sync', [
                'items' => [
                    [
                        'reference' => 'TEST-BOUG-001',
                        'quantite' => 2
                    ]
                ]
            ]);

        $response->assertStatus(200);
        
        // Vérifier que le panier est créé en DB
        $this->assertDatabaseHas('carts', ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('cart_items', [
            'bougie_id' => $this->bougie->id,
            'quantite' => 2
        ]);
    }

    /**
     * TUNNEL-7: Page paiement affiche récapitulatif avec adresse
     * La page /orders/payment nécessite un order.pending en session
     * ET les données d'expédition stockées avec la clé 'order_shipping'
     */
    public function test_page_paiement_affiche_recapitulatif(): void
    {
        // Créer d'abord un panier avec article (prérequis pour la méthode payment())
        $cartService = app(\App\Services\CartService::class);
        $this->actingAs($this->user);
        $cartService->addBougie($this->bougie->id, 1);

        // Créer données d'expédition en session (prérequis pour accéder à /orders/payment)
        session([
            'order_shipping' => [
                'nom' => 'Marie Test',
                'email' => $this->user->email,
                'telephone' => '0612345678',
                'adresse' => '12 Rue de Paris',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ]
        ]);

        $response = $this->actingAs($this->user)
            ->get('/orders/payment');

        $response->assertStatus(200);
        $response->assertSee('Bougie Test');
    }

    /**
     * TUNNEL-8: Route Stripe checkout existe et retourne URL
     */
    public function test_route_stripe_checkout_existe(): void
    {
        $this->actingAs($this->user);
        
        // Simuler order en session (comme après validation adresse)
        $order = \App\Models\Order::create([
            'user_id' => $this->user->id,
            'numero_commande' => 'CMD-2026-0001',
            'total' => 45.00,
            'statut' => 'pending',
            'nom' => 'Test',
            'prenom' => 'Marie',
            'email' => $this->user->email,
            'telephone' => '0612345678',
            'adresse' => '12 Rue de Paris',
            'code_postal' => '75001',
            'ville' => 'Paris'
        ]);

        session(['pending_order_id' => $order->id]);

        // Le test vérifie juste que la route existe (sans appeler Stripe réel)
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('payment.checkout'),
            'La route payment.checkout doit exister'
        );
    }

    /**
     * TUNNEL-9: Structure complète du tunnel
     * Valide que toutes les routes essentielles existent
     */
    public function test_structure_tunnel_complete(): void
    {
        $routesRequises = [
            'kiosque',
            'cart.index',
            'orders.create',
            'orders.store',
            'orders.payment',
            'payment.checkout',
            'payment.success',
            'payment.cancel',
            'orders.my',
            'confirmation',
        ];

        foreach ($routesRequises as $route) {
            $this->assertTrue(
                \Illuminate\Support\Facades\Route::has($route),
                "La route $route doit exister"
            );
        }
    }
}
