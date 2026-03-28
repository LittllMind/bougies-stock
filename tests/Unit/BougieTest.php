<?php

namespace Tests\Unit;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BougieTest extends TestCase
{
    use RefreshDatabase;

<<<<<<< HEAD
    public function test_peut_creer_bougie_avec_factory(): void
    {
        $bougie = Bougie::factory()->create();

        $this->assertDatabaseHas('bougies', [
            'id' => $bougie->id,
            'reference' => $bougie->reference,
        ]);
    }

    public function test_reference_est_unique(): void
    {
        $bougie1 = Bougie::factory()->create(['reference' => 'BOUG-001']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Bougie::factory()->create(['reference' => 'BOUG-001']);
    }

    public function test_detecte_stock_bas(): void
    {
        $bougie = Bougie::factory()->stockBas()->create();

        $this->assertTrue($bougie->isStockBas());
    }

    public function test_detecte_stock_normal(): void
    {
        $bougie = Bougie::factory()->stockOk()->create();

        $this->assertFalse($bougie->isStockBas());
    }

    public function test_calcule_quantite_manquante(): void
    {
        $bougie = Bougie::factory()->create([
            'quantite' => 3,
            'seuil_alerte' => 5,
        ]);

        $this->assertEquals(3, $bougie->getQuantiteManquante());
    }

    public function test_quantite_manquante_retourne_zero_si_stock_ok(): void
    {
        $bougie = Bougie::factory()->create([
            'quantite' => 10,
            'seuil_alerte' => 5,
        ]);

        $this->assertEquals(0, $bougie->getQuantiteManquante());
    }

    public function test_cast_prix_en_decimal(): void
    {
        $bougie = Bougie::factory()->create([
            'prix' => '24.99',
        ]);

        $this->assertEquals(24.99, $bougie->prix);
    }

    public function test_cast_quantite_en_entier(): void
    {
        $bougie = Bougie::factory()->create([
            'quantite' => '15',
        ]);

        $this->assertIsInt($bougie->quantite);
        $this->assertEquals(15, $bougie->quantite);
    }
}
=======
    public function test_peut_creer_une_bougie(): void
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Douce Vanille',
            'prix' => 25.50,
        ]);

        $this->assertDatabaseHas('bougies', [
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Douce Vanille',
            'prix' => 25.50,
        ]);
    }

    public function test_la_factory_genere_10_bougies(): void
    {
        Bougie::factory()->count(10)->create();

        $this->assertEquals(10, Bougie::count());
    }
}
>>>>>>> origin/master
