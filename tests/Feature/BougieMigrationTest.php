<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\Bougie;

class BougieMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_bougies_existe(): void
    {
        $this->assertTrue(Schema::hasTable('bougies'));
    }

    public function test_table_bougies_a_les_colonnes_correctes(): void
    {
        $colonnes = [
            'id',
            'reference',
            'image',
            'parfum',
            'nom',
            'collection',
            'format',
            'type_cire',
            'temps_brulure',
            'notes',
            'prix',
            'quantite',
            'seuil_alerte',
            'created_at',
            'updated_at',
        ];

        foreach ($colonnes as $colonne) {
            $this->assertTrue(
                Schema::hasColumn('bougies', $colonne),
                "La colonne {$colonne} devrait exister dans la table bougies"
            );
        }
    }

    public function test_reference_est_unique(): void
    {
        Bougie::factory()->create(['reference' => 'REF-UNIQUE-TEST']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Bougie::factory()->create(['reference' => 'REF-UNIQUE-TEST']);
    }

    public function test_valeurs_par_defaut_sont_correctes(): void
    {
        $bougie = new Bougie();
        $bougie->reference = 'DEFAULT-TEST-001';
        $bougie->parfum = 'Vanille';
        $bougie->nom = 'Bougie Test Valeurs Défaut';
        $bougie->prix = 25.00;
        $bougie->save();
        
        $bougie->refresh();
        
        $this->assertEquals(0, $bougie->quantite);
        $this->assertEquals(5, $bougie->seuil_alerte);
    }

    public function test_modele_bougie_est_fonctionnel(): void
    {
        Bougie::unguard();
        $bougie = new Bougie([
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Bougie Test',
            'prix' => 25.99,
            'quantite' => 10,
        ]);
        
        $bougie->save();
        Bougie::reguard();
        
        $this->assertDatabaseHas('bougies', [
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Bougie Test',
        ]);
    }

    public function test_factory_bougie_fonctionne(): void
    {
        $bougie = Bougie::factory()->create();
        
        $this->assertInstanceOf(Bougie::class, $bougie);
        $this->assertNotNull($bougie->reference);
        $this->assertNotNull($bougie->parfum);
        $this->assertNotNull($bougie->nom);
        $this->assertNotNull($bougie->prix);
    }
}
