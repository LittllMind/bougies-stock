<?php

namespace Tests\Feature\Client;

use App\Models\User;
use App\Models\Order;
use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'client']);
    }

    /** @test */
    public function client_dashboard_est_accessible_pour_utilisateur_auth()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('client.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('client.dashboard');
    }

    /** @test */
    public function client_dashboard_affiche_stats_commandes()
    {
        $bougie = Bougie::factory()->create(['prix' => 45.00]);
        
        // Créer commande avec statut valide
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 90.00,
            'statut' => 'paid',
            'nom' => 'Test',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);

        // Créer items manuellement
        $order->items()->create([
            'bougie_id' => $bougie->id,
            'quantite' => 2,
            'prix_unitaire' => 45.00,
            'total' => 90.00,
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('client.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('1'); // Nombre commandes
        $response->assertSee('90,00'); // Total dépenses
    }

    /** @test */
    public function client_dashboard_affiche_derniere_commande()
    {
        $bougie = Bougie::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'statut' => 'delivered',
            'total' => 45.00,
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
                         ->get(route('client.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('latestOrder');
        $response->assertSee($order->numero_commande);
        $response->assertSee('Livrée');
    }

    /** @test */
    public function client_dashboard_redirige_guest_vers_login()
    {
        $response = $this->get(route('client.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function client_dashboard_calcule_bougie_preferee()
    {
        $bougiePopulaire = Bougie::factory()->create(['nom' => 'Ganesh']);
        $bougieAutre = Bougie::factory()->create(['nom' => 'Lotus']);
        
        // 2x Ganesh
        $order1 = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 90.00,
            'statut' => 'paid',
            'nom' => 'Test',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
        ]);
        
        $order1->items()->create([
            'bougie_id' => $bougiePopulaire->id,
            'quantite' => 2,
            'prix_unitaire' => 45.00,
            'total' => 90.00,
        ]);
        
        // 1x Lotus
        $order2 = Order::factory()->create([
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
        
        $order2->items()->create([
            'bougie_id' => $bougieAutre->id,
            'quantite' => 1,
            'prix_unitaire' => 45.00,
            'total' => 45.00,
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('client.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('Ganesh');
    }
}