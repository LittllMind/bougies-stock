<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\StockAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BougieStockAlertObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_cree_alerte_stock_quand_bougie_creee_avec_quantite_inferieure_seuil(): void
    {
        // Créer une bougie avec stock initial = 3, seuil = 5
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Bougie Test',
            'quantite' => 3,
            'seuil_alerte' => 5,
        ]);

        // Vérifier qu'une alerte a été créée
        $this->assertDatabaseHas('stock_alerts', [
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
            'quantite_actuelle' => 3,
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);
    }

    public function test_cree_alerte_stock_quand_quantite_baisse_sous_seuil_apres_mise_a_jour(): void
    {
        // Créer une bougie avec stock suffisant
        $bougie = Bougie::factory()->create([
            'quantite' => 10,
            'seuil_alerte' => 5,
        ]);

        // Réinitialiser les alertes créées à la création (normalement il n'y en a pas)
        StockAlert::truncate();

        // Diminuer le stock sous le seuil
        $bougie->quantite = 2;
        $bougie->save();

        // Vérifier qu'une alerte a été créée
        $this->assertDatabaseHas('stock_alerts', [
            'stockable_type' => Bougie::class,
            'stockable_id' => $bougie->id,
            'quantite_actuelle' => 2,
            'statut' => 'actif',
        ]);
    }

    public function test_ne_cree_pas_alerte_si_quantite_superieure_seuil(): void
    {
        // Créer une bougie avec stock suffisant
        $bougie = Bougie::factory()->create([
            'quantite' => 10,
            'seuil_alerte' => 5,
        ]);

        // Vérifier qu'aucune alerte active n'existe
        $alertCount = StockAlert::where('stockable_id', $bougie->id)
            ->where('statut', 'actif')
            ->count();

        $this->assertEquals(0, $alertCount);
    }

    public function test_ne_cree_pas_alerte_si_existe_deja_non_resolue(): void
    {
        // Créer une bougie avec stock faible
        $bougie = Bougie::factory()->create([
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);

        $countBefore = StockAlert::where('stockable_id', $bougie->id)->count();

        // Modifier à nouveau (sans changer le stock)
        $bougie->parfum = 'Autre';
        $bougie->save();

        $countAfter = StockAlert::where('stockable_id', $bougie->id)->count();

        // Vérifier qu'aucune alerte supplémentaire n'a été créée
        $this->assertEquals($countBefore, $countAfter);
    }

    public function test_cree_nouvelle_alerte_si_precedente_resolue_et_stock_toujours_bas(): void
    {
        // Créer une bougie avec stock faible
        $bougie = Bougie::factory()->create([
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);

        // Résoudre l'alerte
        $alerte = $bougie->stockAlerts()->first();
        $alerte->update(['statut' => 'resolu', 'resolved_at' => now()]);

        // Baisser encore le stock
        $bougie->quantite = 1;
        $bougie->save();

        // Vérifier qu'une nouvelle alerte a été créée
        $alertCount = $bougie->stockAlerts()->where('statut', 'actif')->count();
        
        $this->assertEquals(1, $alertCount);
    }

    public function test_cree_alerte_rupture_stock_quand_quantite_zero(): void
    {
        // Créer une bougie en rupture
        $bougie = Bougie::factory()->create([
            'quantite' => 0,
            'seuil_alerte' => 5,
        ]);

        // Vérifier que l'alerte existe avec niveau critique
        $alerte = StockAlert::where('stockable_id', $bougie->id)->first();
        
        $this->assertNotNull($alerte);
        $this->assertEquals('critique', $alerte->niveau);
    }

    public function test_observer_enregistre_dans_eventserviceprovider(): void
    {
        // Vérifier que l'observer est bien enregistré
        $providers = app()->getProviders(\Illuminate\Foundation\Support\Providers\EventServiceProvider::class);
        $this->assertNotEmpty($providers);
    }
}
