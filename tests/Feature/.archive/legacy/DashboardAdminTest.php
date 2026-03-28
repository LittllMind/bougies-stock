<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_affiche_statistiques_ventes()
    {
        $admin = User::factory()->admin()->create();
        
        // Créer commandes et items
        $bougie = Bougie::factory()->create(['prix' => 25.00]);
        $order = Order::factory()->create([
            'statut' => 'payee',
            'total' => 50.00,
            'created_at' => Carbon::today(),
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'bougie_id' => $bougie->id,
            'quantite' => 2,
            'prix_unitaire' => 25.00,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Ventes');
    }

    /** @test */
    public function dashboard_affiche_nombre_commandes_du_jour()
    {
        $admin = User::factory()->admin()->create();
        
        Order::factory()->count(3)->create([
            'statut' => 'payee',
            'created_at' => Carbon::today(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Commandes');
    }

    /** @test */
    public function dashboard_affiche_produits_plus_vendus()
    {
        $admin = User::factory()->admin()->create();
        
        $bougie1 = Bougie::factory()->create(['nom' => 'Bougie Vanille', 'reference' => 'VAN-001']);
        $bougie2 = Bougie::factory()->create(['nom' => 'Bougie Lavande', 'reference' => 'LAV-001']);
        
        $order1 = Order::factory()->create(['statut' => 'payee']);
        OrderItem::factory()->create(['order_id' => $order1->id, 'bougie_id' => $bougie1->id, 'quantite' => 5]);
        
        $order2 = Order::factory()->create(['statut' => 'payee']);
        OrderItem::factory()->create(['order_id' => $order2->id, 'bougie_id' => $bougie2->id, 'quantite' => 2]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Top produits');
    }

    /** @test */
    public function dashboard_affiche_alertes_stock_faible()
    {
        $admin = User::factory()->admin()->create();
        
        // Créer une bougie avec stock faible
        Bougie::factory()->create([
            'nom' => 'Bougie Alerte',
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Alertes stock');
    }

    /** @test */
    public function dashboard_affiche_commandes_recentes()
    {
        $admin = User::factory()->admin()->create();
        
        Order::factory()->count(5)->create(['statut' => 'payee']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Commandes récentes');
    }

    /** @test */
    public function dashboard_requiert_authentification_admin()
    {
        $user = User::factory()->client()->create();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect();
    }

    /** @test */
    public function dashboard_affiche_statistiques_periode()
    {
        $admin = User::factory()->admin()->create();
        
        // Commandes cette semaine
        Order::factory()->count(3)->create([
            'statut' => 'payee',
            'created_at' => Carbon::now()->subDays(2),
            'total' => 100.00,
        ]);
        
        // Commande mois dernier
        Order::factory()->create([
            'statut' => 'payee',
            'created_at' => Carbon::now()->subMonth(),
            'total' => 50.00,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard?periode=semaine');

        $response->assertSuccessful();
        $response->assertSee('Bilan');
    }

    /** @test */
    public function dashboard_affiche_revenus_total()
    {
        $admin = User::factory()->admin()->create();
        
        Order::factory()->count(2)->create([
            'statut' => 'payee',
            'total' => 100.00,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function dashboard_affiche_nouveaux_clients()
    {
        $admin = User::factory()->admin()->create();
        
        User::factory()->count(3)->create([
            'created_at' => Carbon::today(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Nouveaux');
    }
}
