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
        $table = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('bougies');
        
        $this->assertTrue($table->hasIndex('bougies_reference_unique'));
    }

    public function test_valeurs_par_defaut_sont_correctes(): void
    {
        $table = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('bougies');
        $quantiteColumn = $table->getColumn('quantite');
        $seuilAlerteColumn = $table->getColumn('seuil_alerte');

        $this->assertEquals(0, $quantiteColumn->getDefault());
        $this->assertEquals(5, $seuilAlerteColumn->getDefault());
    }
}
