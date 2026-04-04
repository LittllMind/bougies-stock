<?php

namespace Tests\Feature\Admin;

use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEnhancedTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function dashboard_displays_sales_today_card()
    {
        // Créer une commande payée aujourd'hui
        $order = Order::factory()->create([
            'statut' => 'paid',
            'total' => 150.50,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Ventes du jour')
            ->assertSee('150,50');
    }

    /** @test */
    public function dashboard_displays_low_stock_alert_card()
    {
        // Créer des produits avec stock faible (< 5)
        Bougie::factory()->create(['quantite' => 3, 'seuil_alerte' => 5]);
        Bougie::factory()->create(['quantite' => 2, 'seuil_alerte' => 5]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Stock faible')
            ->assertSee('2'); // 2 produits en stock faible
    }

    /** @test */
    public function dashboard_displays_pending_orders_card()
    {
        // Créer des commandes en attente
        Order::factory()->create(['statut' => 'pending', 'created_at' => now()]);
        Order::factory()->create(['statut' => 'pending', 'created_at' => now()]);
        Order::factory()->create(['statut' => 'paid', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Commandes en attente')
            ->assertSee('2'); // 2 commandes en attente
    }

    /** @test */
    public function dashboard_displays_sales_chart_30_days()
    {
        // Créer des ventes sur les 30 derniers jours
        for ($i = 0; $i < 30; $i++) {
            Order::factory()->create([
                'statut' => 'paid',
                'total' => 100 + $i,
                'created_at' => now()->subDays($i),
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Ventes sur 30 jours')
            ->assertSee('ventesChart'); // ID du canvas pour le graphique
    }

    /** @test */
    public function dashboard_displays_recent_orders_table()
    {
        // Créer des commandes récentes
        $user = User::factory()->create(['name' => 'Jean Dupont']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'numero_commande' => 'ORD-2024-001',
            'total' => 99.99,
            'statut' => 'paid',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Dernières commandes')
            ->assertSee('ORD-2024-001')
            ->assertSee('Jean Dupont')
            ->assertSee('99,99');
    }

    /** @test */
    public function recent_orders_have_links_to_details()
    {
        $order = Order::factory()->create([
            'numero_commande' => 'ORD-TEST-001',
            'statut' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Voir les détails')
            ->assertSee(route('admin.orders.show', $order));
    }

    /** @test */
    public function dashboard_displays_critical_stock_alerts()
    {
        // Créer des produits avec stock critique (< 5)
        Bougie::factory()->create(['nom' => 'Bougie Rose', 'quantite' => 4, 'seuil_alerte' => 5]);
        Bougie::factory()->create(['nom' => 'Bougie Bleue', 'quantite' => 1, 'seuil_alerte' => 5]);
        Bougie::factory()->create(['nom' => 'Bougie Normale', 'quantite' => 10, 'seuil_alerte' => 5]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Alertes stock critique')
            ->assertSee('Bougie Rose')
            ->assertSee('Bougie Bleue')
            ->assertDontSee('Bougie Normale'); // Stock normal
    }

    /** @test */
    public function dashboard_shows_zero_for_empty_data()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('0,00') // Ventes du jour = 0
            ->assertSee('Aucune commande récente');
    }

    /** @test */
    public function dashboard_uses_simple_chart_not_complex_library()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        // Vérifier qu'on n'utilise pas Chart.js complexe
        $response->assertStatus(200)
            ->assertDontSee('chart.js')
            ->assertDontSee('Chart.js');
    }

    /** @test */
    public function dashboard_includes_link_to_all_orders()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Voir toutes les commandes')
            ->assertSee(route('admin.orders.index'));
    }

    /** @test */
    public function dashboard_includes_link_to_stock_management()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Gérer le stock')
            ->assertSee(route('admin.bougies.index'));
    }
}
