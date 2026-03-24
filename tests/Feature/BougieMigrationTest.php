<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

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
        // Créer une bougie de référence
        \App\Models\Bougie::factory()->create(['reference' => 'REF-UNIQUE-TEST']);
        
        // Tenter de créer une bougie avec la même référence doit échouer
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        \App\Models\Bougie::factory()->create(['reference' => 'REF-UNIQUE-TEST']);
    }

    public function test_valeurs_par_defaut_sont_correctes(): void
    {
        // Créer une bougie avec seulement les champs requis
        $bougie = new \App\Models\Bougie();
        $bougie->reference = 'DEFAULT-TEST-001';
        $bougie->parfum = 'Vanille';
        $bougie->nom = 'Bougie Test Valeurs Défaut';
        $bougie->prix = 25.00;
        $bougie->save();
        
        // Recharger depuis la base
        $bougie->refresh();
        
        // Vérifier les valeurs par défaut
        $this->assertEquals(0, $bougie->quantite);
        $this->assertEquals(5, $bougie->seuil_alerte);
    }
}
