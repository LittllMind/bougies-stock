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
        
        // Vider la session entre chaque test pour éviter les conflits avec pending_order_id
        session()->flush();
        
        // Supprimer explicitement pending_order_id pour éviter les conflits entre tests
        session()->forget('pending_order_id');
        \Session::forget('pending_order_id');
        
        $this->cartService = app(CartService::class);
        
        // Créer une bougie pour les tests
        $this->bougie = Bougie::factory()->create([
            'nom' => 'Ganesh',
            'reference' => 'BOUG-001',
            'prix' => 45.00,
            'quantite' => 10,
            'parfum' => 'Santal',
            'collection' => 'Spirit',
        ]);
        
        // Créer un utilisateur
        $this->user = User::factory()->create();
    }

    private function clearSessionForNewOrder()
    {
        session()->forget(['pending_order_id', 'order_shipping', 'order_billing', 'cart_id']);
    }

    protected function addBougieToCart($quantite = 1)
    {
        // Utiliser CartService comme OrderController
        $this->actingAs($this->user);
        
        $cart = $this->cartService->getCart();
        CartItem::create([
            'cart_id' => $cart->id,
            'bougie_id' => $this->bougie->id,
            'vinyle_id' => null,
            'quantite' => $quantite,
            'prix_unitaire' => $this->bougie->prix,
        ]);
        
        return $cart;
    }

    /** @test */
    public function test_page_checkout_affiche_panier_avec_bougies()
    {
        // Ajouter une bougie au panier DB
        $this->addBougieToCart(2);

        $response = $this->actingAs($this->user)
            ->get(route('orders.create'));

        $response->assertStatus(200);
        $response->assertViewIs('orders.create');
        $response->assertSee('Ganesh');
    }

    /** @test */
    public function test_checkout_requiert_panier_non_vide()
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.create'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_checkout_stocke_adresse_livraison_en_session()
    {
        // Ajouter une bougie au panier
        $this->addBougieToCart(1);

        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), [
                'nom' => 'Jean Dupont',
                'email' => 'jean@example.com',
                'telephone' => '0612345678',
                'adresse' => '123 Rue de la Paix',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'pays' => 'FR',
            ]);

        $response->assertRedirect(route('orders.payment'));
        $response->assertSessionHas('order_shipping');
    }

    /** @test */
    public function test_page_paiement_affiche_recapitulatif_commande()
    {
        // Nettoyer session pour éviter réutilisation commande précédente
        $this->clearSessionForNewOrder();
        // Utiliser actingAs ET flush sans perdre l'auth qui vient de http basic
        $this->actingAs($this->user);
        
        // Nettoyer la session sans perdre l'auth
        \Session::forget(['order_shipping', 'order_billing', 'pending_order_id', 'cart']);

        // Ajouter une bougie au panier
        $this->addBougieToCart(2);

        // Simuler les données en session
        session()->put('order_shipping', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        // Billing aussi requis
        session()->put('order_billing', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.payment'));

        $response->assertStatus(200);
        $response->assertSee('Jean Dupont');
    }

    /** @test */
    public function test_cree_commande_avec_bougies()
    {
        // Nettoyer session pour éviter réutilisation commande précédente
        $this->clearSessionForNewOrder();
        // Ajouter une bougie au panier
        $this->addBougieToCart(2);

        session()->put('order_shipping', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        session()->put('order_billing', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.payment'));

        // La commande doit être créée
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'statut' => 'en_attente',
        ]);

        // Les order items doivent être créés avec les infos bougie
        $this->assertDatabaseHas('order_items', [
            'bougie_id' => $this->bougie->id,
            'quantite' => 2,
            'prix_unitaire' => 45.00,
        ]);
    }

    /** @test */
    public function test_commande_decremente_stock_bougie()
    {
        // Nettoyer session pour éviter réutilisation commande précédente
        $this->clearSessionForNewOrder();
        
        // Force re-auth pour nouvelle commande
        session()->flush();
        $this->actingAs($this->user);

        // Réinitialiser la bougie avec stock 10
        $this->bougie->quantite = 10;
        $this->bougie->save();

        // Ajouter une bougie au panier
        $this->addBougieToCart(3);

        session()->put('order_shipping', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        session()->put('order_billing', [
            'nom' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $this->actingAs($this->user)
            ->get(route('orders.payment'));

        // Le stock doit être décrémenté
        $this->bougie->refresh();
        $this->assertEquals(7, $this->bougie->quantite); // 10 - 3
    }

    /** @test */
    public function test_paiement_refuse_panier_vide()
    {
        session()->put('order_shipping', ['nom' => 'Test']);

        $response = $this->actingAs($this->user)
            ->get(route('orders.payment'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_paiement_refuse_sans_adresse_livraison()
    {
        // Ajouter une bougie au panier
        $this->addBougieToCart(1);

        // Pas de session shipping
        $response = $this->actingAs($this->user)
            ->get(route('orders.payment'));

        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHas('error');
    }
}
