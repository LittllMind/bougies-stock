<?php

namespace Tests\Feature\Admin;

use App\Models\Bougie;
use App\Models\StockAlert;
use App\Models\User;
use App\Models\MouvementStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    /** @test */
    public function test_dashboard_displays_statistics()
    {
        // Créer des données de test
        Bougie::factory()->create([
            'quantite' => 10,
            'prix' => 25.50,
        ]);
        Bougie::factory()->create([
            'quantite' => 5,
            'prix' => 30.00,
        ]);
        $bougieAlerte = Bougie::factory()->create([
            'quantite' => 2,
            'prix' => 15.00,
            'seuil_alerte' => 3,
        ]);

        // Créer une alerte stock
        StockAlert::factory()->create([
            'stockable_type' => 'App\Models\Bougie',
            'stockable_id' => $bougieAlerte->id,
            'quantite_actuelle' => 2,
            'seuil_alerte' => 3,
            'statut' => 'actif',
        ]);

        // Créer des mouvements de stock
        MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'vinyle',
            'produit_id' => 1,
            'quantite' => 10,
            'date_mouvement' => now(),
        ]);
        MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'vinyle',
            'produit_id' => 1,
            'quantite' => 5,
            'date_mouvement' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard.index');

        // Vérifier les stats affichées dans la vue
        $response->assertSee('3'); // Nombre total de bougies
        // Valeur stock: (10*25.50) + (5*30) + (2*15) = 255 + 150 + 30 = 435
        $response->assertSee('435,00'); // Valeur stock total
        $response->assertSee('1'); // Alertes actives
        $response->assertSee('Entrée'); // Type de mouvement
        $response->assertSee('Sortie'); // Type de mouvement
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard.index');
    }

    /** @test */
    public function employe_can_access_dashboard()
    {
        $response = $this->actingAs($this->employe)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard.index');
    }

    /** @test */
    public function client_cannot_access_dashboard()
    {
        $response = $this->actingAs($this->client)
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('catalogue'));
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
