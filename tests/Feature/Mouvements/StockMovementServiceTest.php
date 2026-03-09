
<?php

namespace Tests\Feature\Mouvements;

use Tests\TestCase;
use App\Models\User;
use App\Models\Fond;
use App\Models\Vinyle;
use App\Models\MouvementStock;
use App\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class StockMovementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockMovementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockMovementService();
    }

    /** @test */
    public function service_peut_incrementer_quantite_fond()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->miroir()->create(['quantite' => 10]);

        $mouvement = $this->service->incrementerFond($fond, 5, $admin, 'TEST-001');

        $this->assertEquals(15, $fond->fresh()->quantite);
        $this->assertEquals(5, $mouvement->quantite);
        $this->assertEquals('entree', $mouvement->type);
        $this->assertEquals('miroir', $mouvement->produit_type);
    }

    /** @test */
    public function service_peut_decrementer_quantite_fond()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->miroir()->create(['quantite' => 10]);

        $mouvement = $this->service->decrementerFond($fond, 3, $admin, 'SORTIE-001');

        $this->assertEquals(7, $fond->fresh()->quantite);
        $this->assertEquals(3, $mouvement->quantite);
        $this->assertEquals('sortie', $mouvement->type);
    }

    /** @test */
    public function service_empêche_decrement_inferieur_a_zero()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->miroir()->create(['quantite' => 2]);

        $mouvement = $this->service->decrementerFond($fond, 5, $admin, 'SORTIE-001');

        // Doit bloquer ou ajuster - selon implémentation
        $this->assertGreaterThanOrEqual(0, $fond->fresh()->quantite);
    }

    /** @test */
    public function service_enregistre_entree_stock_vinyle()
    {
        $admin = $this->adminUser();
        $vinyle = Vinyle::factory()->create(['quantite_stock' => 5]);

        $mouvement = $this->service->entreeStock($vinyle, 10, $admin, 'FOURN-2026-001');

        $this->assertEquals(15, $vinyle->fresh()->quantite_stock);
        $this->assertEquals(10, $mouvement->quantite);
        $this->assertEquals('entree', $mouvement->type);
        $this->assertEquals('vinyle', $mouvement->produit_type);
    }

    /** @test */
    public function service_enregistre_sortie_stock_vinyle()
    {
        $admin = $this->adminUser();
        $vinyle = Vinyle::factory()->create(['quantite_stock' => 20]);

        $mouvement = $this->service->sortieStock($vinyle, 2, $admin, 'CMD-2026-001');

        $this->assertEquals(18, $vinyle->fresh()->quantite_stock);
        $this->assertEquals(2, $mouvement->quantite);
        $this->assertEquals('sortie', $mouvement->type);
    }

    /** @test */
    public function service_mouvement_a_utilisateur_connecte()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create();

        $mouvement = $this->service->incrementerFond($fond, 5, $admin);

        $this->assertEquals($admin->id, $mouvement->user_id);
    }

    /** @test */
    public function service_mouvement_a_date_correcte()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create();

        $before = now();
        $mouvement = $this->service->incrementerFond($fond, 5, $admin);
        $after = now();

        $this->assertTrue($mouvement->date_mouvement->between($before, $after));
    }

    /** @test */
    public function service_mouvement_a_reference_optionnelle()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create();

        // Sans référence
        $mouvement1 = $this->service->incrementerFond($fond, 5, $admin);
        $this->assertNull($mouvement1->reference);

        // Avec référence
        $mouvement2 = $this->service->incrementerFond($fond, 3, $admin, 'REF-123');
        $this->assertEquals('REF-123', $mouvement2->reference);
    }

    /** @test */
    public function service_mouvement_a_notes_optionnelles()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create();

        $mouvement = $this->service->incrementerFond($fond, 5, $admin, 'REF', 'Notes de test');

        $this->assertEquals('Notes de test', $mouvement->notes);
    }

    /** @test */
    public function service_cree_transaction_database_coherente()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->miroir()->create(['quantite' => 10]);

        $mouvement = $this->service->incrementerFond($fond, 5, $admin);

        // Vérifier en base
        $this->assertDatabaseHas('mouvements_stock', [
            'id' => $mouvement->id,
            'type' => 'entree',
            'produit_type' => 'miroir',
            'produit_id' => $fond->id,
            'quantite' => 5,
            'user_id' => $admin->id,
        ]);

        // Vérifier le stock a bien été mis à jour
        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 15,
        ]);
    }

    /** @test */
    public function service_rollback_en_cas_erreur()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create(['quantite' => 10]);

        $initialCount = MouvementStock::count();
        $initialQuantite = $fond->fresh()->quantite;

        // Simuler une erreur (forcer exception)
        try {
            DB::transaction(function () use ($fond, $admin) {
                $this->service->incrementerFond($fond, 5, $admin, 'REF-ERROR');
                throw new \Exception('Erreur forcée');
            });
        } catch (\Exception $e) {
            // Ignorer l'exception
        }

        // Vérifier rollback
        $this->assertEquals($initialCount, MouvementStock::count());
        $this->assertEquals($initialQuantite, $fond->fresh()->quantite);
    }

    /** @test */
    public function service_incrementer_fond_dore_fonctionne()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->dore()->create(['quantite' => 5]);

        $mouvement = $this->service->incrementerFond($fond, 10, $admin);

        $this->assertEquals(15, $fond->fresh()->quantite);
        $this->assertEquals('dore', $mouvement->produit_type);
    }

    /** @test */
    public function service_peut_traiter_grandes_quantites()
    {
        $admin = $this->adminUser();
        $fond = Fond::factory()->create(['quantite' => 1000]);

        $mouvement = $this->service->incrementerFond($fond, 5000, $admin);

        $this->assertEquals(6000, $fond->fresh()->quantite);
        $this->assertEquals(5000, $mouvement->quantite);
    }
}
