<?php

namespace Tests\Feature;

use App\Models\Vinyle;
use App\Models\StockAlert;
use Tests\TestCase;

class StockAlertTest extends TestCase
{
    public function test_stock_alert_created_for_critical_product(): void
    {
        $produit = Vinyle::factory()->create([
            'quantite' => 0,
            'seuil_alerte' => 3
        ]);

        $this->artisan('stock:check-critical');

        $this->assertDatabaseHas('stock_alerts', [
            'produit_id' => $produit->id,
            'statut' => 'actif'
        ]);
    }
}
