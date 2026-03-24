<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\StockAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAlertControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employe;
    protected Bougie $bougie;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employe = User::factory()->create(['role' => 'employe']);
        
        $this->bougie = Bougie::factory()->create([
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);
    }

    public function test_admin_peut_voir_liste_alertes()
    {
        $alerte = StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.stock-alerts.index');
        $response->assertSee('Alertes de Stock');
    }

    public function test_employe_peut_voir_liste_alertes()
    {
        StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->employe)
                         ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200);
    }

    public function test_utilisateur_standard_ne_peut_pas_voir_alertes()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
                         ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(302); // Redirection vers login pour utilisateur non autorisé
    }

    public function test_admin_peut_voir_detail_alerte()
    {
        $alerte = StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.stock-alerts.show', $alerte));

        $response->assertStatus(200);
        $response->assertViewIs('admin.stock-alerts.show');
        $response->assertSee($this->bougie->nom);
    }

    public function test_admin_peut_marquer_alerte_comme_resolue()
    {
        $alerte = StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
            'resolue' => false,
        ]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.stock-alerts.resolve', $alerte));

        $response->assertRedirect(route('admin.stock-alerts.index'));
        
        $alerteFresh = $alerte->fresh();
        $this->assertTrue((bool)$alerteFresh->resolue);
        $this->assertNotNull($alerteFresh->resolved_at);
    }

    public function test_admin_peut_supprimer_alerte()
    {
        $alerte = StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.stock-alerts.destroy', $alerte));

        $response->assertRedirect(route('admin.stock-alerts.index'));
        $this->assertDatabaseMissing('stock_alerts', ['id' => $alerte->id]);
    }

    public function test_liste_alertes_affiche_nombre_alertes_en_attente()
    {
        // Supprimer les alertes créées automatiquement par setUp (l'observer)
        StockAlert::query()->delete();

        // Alerte en attente
        StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $this->bougie->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 5,
            'statut' => 'actif',
            'resolue' => false,
        ]);

        // Alerte résolue
        $bougie2 = Bougie::factory()->create();
        StockAlert::create([
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie2->id,
            'quantite_actuelle' => 1,
            'seuil_alerte' => 5,
            'statut' => 'resolu',
            'resolue' => true,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.stock-alerts.index'));

        $response->assertStatus(200);
        $response->assertSee('1 en attente');
    }
}
