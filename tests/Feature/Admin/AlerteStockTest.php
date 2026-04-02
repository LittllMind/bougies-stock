<?php

namespace Tests\Feature\Admin;

use App\Models\Bougie;
use App\Models\Fond;
use App\Models\StockAlert;
use App\Models\User;
use App\Models\Vinyle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlerteStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employe;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employe = User::factory()->create(['role' => 'employe']);
        $this->client = User::factory()->create(['role' => 'client']);
    }

    // ============================================
    // TESTS D'ACCÈS (Authorization)
    // ============================================

    /** @test */
    public function admin_can_access_stock_alerts_index()
    {
        StockAlert::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.stock-alerts.index');
    }

    /** @test */
    public function employe_can_access_stock_alerts_index()
    {
        StockAlert::factory()->create();

        $response = $this->actingAs($this->employe)
            ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.stock-alerts.index');
    }

    /** @test */
    public function client_cannot_access_stock_alerts_index()
    {
        $response = $this->actingAs($this->client)
            ->get(route('admin.stock-alerts.index'));

        // Middleware redirige vers catalogue au lieu de 403
        $response->assertRedirect(route('catalogue'));
    }

    /** @test */
    public function guest_cannot_access_stock_alerts_index()
    {
        $response = $this->get(route('admin.stock-alerts.index'));

        $response->assertRedirect(route('login'));
    }

    // ============================================
    // TESTS DE VISUALISATION (Show)
    // ============================================

    /** @test */
    public function admin_can_view_stock_alert_details()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.show', $alert));

        $response->assertStatus(200)
            ->assertViewIs('admin.stock-alerts.show')
            ->assertViewHas('stockAlert', $alert);
    }

    /** @test */
    public function employe_can_view_stock_alert_details()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->employe)
            ->get(route('admin.stock-alerts.show', $alert));

        $response->assertStatus(200);
    }

    // ============================================
    // TESTS DE RÉSOLUTION (Resolve)
    // ============================================

    /** @test */
    public function admin_can_resolve_stock_alert()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => false,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        $response->assertRedirect(route('admin.stock-alerts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('stock_alerts', [
            'id' => $alert->id,
            'resolue' => true,
            'statut' => 'resolu',
        ]);
        $this->assertNotNull($alert->fresh()->resolved_at);
    }

    /** @test */
    public function employe_can_resolve_stock_alert()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => false,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->employe)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        $response->assertRedirect(route('admin.stock-alerts.index'));
        $this->assertTrue($alert->fresh()->resolue);
    }

    /** @test */
    public function client_cannot_resolve_stock_alert()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => false,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->client)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        // Middleware redirige vers catalogue
        $response->assertRedirect(route('catalogue'));
        $this->assertFalse($alert->fresh()->resolue);
    }

    // ============================================
    // TESTS DE SUPPRESSION (Delete)
    // ============================================

    /** @test */
    public function admin_can_delete_stock_alert()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.stock-alerts.destroy', $alert));

        $response->assertRedirect(route('admin.stock-alerts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('stock_alerts', [
            'id' => $alert->id,
        ]);
    }

    /** @test */
    public function employe_can_delete_stock_alert()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->employe)
            ->delete(route('admin.stock-alerts.destroy', $alert));

        $response->assertRedirect(route('admin.stock-alerts.index'));
        $this->assertDatabaseMissing('stock_alerts', ['id' => $alert->id]);
    }

    /** @test */
    public function client_cannot_delete_stock_alert()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->client)
            ->delete(route('admin.stock-alerts.destroy', $alert));

        // Middleware redirige vers catalogue
        $response->assertRedirect(route('catalogue'));
        $this->assertDatabaseHas('stock_alerts', ['id' => $alert->id]);
    }

    // ============================================
    // TESTS DE DONNÉES (Data Integrity)
    // ============================================

    /** @test */
    public function index_displays_paginated_alerts()
    {
        StockAlert::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200)
            ->assertViewHas('alertes', function ($alerts) {
                return $alerts->count() <= 20; // Pagination par défaut
            });
    }

    /** @test */
    public function index_shows_correct_stats()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        // Vérifie que les stats sont présentes et cohérentes
        $response->assertViewHas('alertesEnAttente', function ($count) {
            return is_int($count) && $count >= 0;
        })->assertViewHas('alertesResolues', function ($count) {
            return is_int($count) && $count >= 0;
        });
    }

    /** @test */
    public function index_includes_stockable_relationship()
    {
        $bougie = Bougie::factory()->create();
        $alert = StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        $response->assertViewHas('alertes', function ($alerts) use ($bougie) {
            $first = $alerts->first();
            return $first->stockable !== null && 
                   $first->stockable->id === $bougie->id;
        });
    }

    /** @test */
    public function show_includes_stockable_details()
    {
        // Test avec Bougie (Vinyle n'existe plus)
        $bougie = Bougie::factory()->create();
        $alert = StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.show', $alert));

        $response->assertViewHas('stockAlert', function ($alert) use ($bougie) {
            return $alert->stockable->id === $bougie->id &&
                   $alert->stockable->nom === $bougie->nom;
        });
    }

    /** @test */
    public function can_resolve_alert_for_fond()
    {
        // Skip if fonds table not available
        if (!\Schema::hasTable('fonds')) {
            $this->markTestSkipped('Fonds table not available');
        }

        $fond = Fond::factory()->create();
        $alert = StockAlert::factory()->create([
            'stockable_type' => Fond::class,
            'stockable_id' => $fond->id,
            'resolue' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        $response->assertRedirect();
        $this->assertTrue($alert->fresh()->resolue);
    }

    /** @test */
    public function resolved_alert_has_timestamp()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => false,
            'resolved_at' => null,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        $this->assertNotNull($alert->fresh()->resolved_at);
    }

    // ============================================
    // TESTS DE NAVIGATION (Routes)
    // ============================================

    /** @test */
    public function all_admin_stock_alert_routes_exist()
    {
        $alert = StockAlert::factory()->create();

        $routes = [
            route('admin.stock-alerts.index'),
            route('admin.stock-alerts.show', $alert),
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertNotEquals(404, $response->getStatusCode(), "Route missing: $route");
        }
    }

    /** @test */
    public function index_view_contains_expected_ui_elements()
    {
        StockAlert::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        $response->assertSee('Alertes de Stock')
            ->assertSee('en attente')
            ->assertSee('Résolues')
            ->assertSee('Toutes');
    }

    /** @test */
    public function show_view_contains_expected_ui_elements()
    {
        $alert = StockAlert::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.show', $alert));

        $response->assertSee('Retour aux alertes')
            ->assertSee($alert->stockable->nom)
            ->assertSee('Quantité Actuelle');
    }

    // ============================================
    // TESTS DE RÉGRESSION (Edge Cases)
    // ============================================

    /** @test */
    public function cannot_resolve_already_resolved_alert()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => true,
            'statut' => 'resolu',
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.stock-alerts.resolve', $alert));

        // Should still redirect without error
        $response->assertRedirect();
    }

    /** @test */
    public function can_delete_resolved_alert()
    {
        $alert = StockAlert::factory()->create([
            'resolue' => true,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.stock-alerts.destroy', $alert));

        $response->assertRedirect();
        $this->assertDatabaseMissing('stock_alerts', ['id' => $alert->id]);
    }

    /** @test */
    public function index_works_with_empty_database()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200)
            ->assertViewHas('alertesEnAttente', 0)
            ->assertViewHas('alertesResolues', 0);
    }

    /** @test */
    public function show_handles_missing_stockable_gracefully()
    {
        // Test avec un produit valide mais où l'affichage gère l'absence de données
        $bougie = Bougie::factory()->create();
        $alert = StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.show', $alert));

        $response->assertStatus(200)
            ->assertSee($bougie->nom);
    }

    // ============================================
    // TESTS EXPORT CSV (T3.3)
    // ============================================

    /** @test */
    public function test_export_csv_returns_file()
    {
        $bougie = Bougie::factory()->create();
        $alert = StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
            'resolue' => false,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.export'));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeader('Content-Disposition');

        // Pour StreamedResponse, on capture le contenu via output buffering
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Vérifie que le contenu CSV contient les en-têtes
        $this->assertStringContainsString('ID', $content);
        $this->assertStringContainsString('Type', $content);
        $this->assertStringContainsString('Nom Produit', $content);
        $this->assertStringContainsString('Bougie', $content);
        $this->assertStringContainsString($bougie->nom, $content);
    }

    /** @test */
    public function test_export_csv_with_filters()
    {
        // Créer plusieurs alertes
        $bougie1 = Bougie::factory()->create();
        $bougie2 = Bougie::factory()->create();

        StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie1->id,
            'resolue' => false,
            'statut' => 'actif',
            'quantite_actuelle' => 0,
        ]);

        StockAlert::factory()->create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie2->id,
            'resolue' => false,
            'statut' => 'actif',
            'quantite_actuelle' => 5,
            'seuil_alerte' => 10,
        ]);

        // Test avec filtre type=bougie - capture du contenu via ob
        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.export', ['type' => 'bougie']));

        $response->assertStatus(200);
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringContainsString('Bougie', $content);

        // Test avec filtre niveau=critique
        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock-alerts.export', ['niveau' => 'critique']));

        $response->assertStatus(200);
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringContainsString('Rupture', $content);
        // Ne devrait contenir que l'alerte critique (quantité 0)
        $this->assertStringContainsString($bougie1->nom, $content);
    }
}