<?php

namespace Tests\Feature\Client;

use App\Models\User;
use App\Models\Order;
use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'client']);
    }

    /** @test */
    public function mes_commandes_est_accessible_pour_client_auth()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('orders.my'));

        $response->assertStatus(200);
        $response->assertViewIs('orders.my-orders');
    }

    /** @test */
    public function mes_commandes_affiche_liste_commandes_client()
    {
        $bougie = Bougie::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 45.00,
            'statut' => 'paid',
            'nom' => 'Test',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $order->items()->create([
            'bougie_id' => $bougie->id,
            'quantite' => 1,
            'prix_unitaire' => 45.00,
            'total' => 45.00,
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('orders.my'));

        $response->assertStatus(200);
        $response->assertSee($order->numero_commande);
        $response->assertSee($bougie->nom);
        $response->assertSee('Payée');
    }

    /** @test */
    public function mes_commandes_redirige_guest_vers_login()
    {
        $response = $this->get(route('orders.my'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function mes_commandes_affiche_message_si_aucune_commande()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('orders.my'));

        $response->assertStatus(200);
        $response->assertSee('Aucune commande pour le moment');
    }

    /** @test */
    public function mes_commandes_ne_montre_pas_commandes_autres_clients()
    {
        $otherUser = User::factory()->create();
        
        Order::factory()->create([
            'user_id' => $otherUser->id,
            'total' => 100.00,
            'statut' => 'paid',
            'nom' => 'Other',
            'email' => 'other@test.com',
            'telephone' => '0123456789',
            'adresse' => '456 Other',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('orders.my'));

        $response->assertStatus(200);
        $response->assertSee('Aucune commande pour le moment');
    }
}