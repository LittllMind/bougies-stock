<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bougie;

class BougieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 10 bougies avec des données aléatoires
        Bougie::factory()->count(10)->create();

        // Créer quelques bougies spécifiques pour les tests
        Bougie::factory()->create([
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Vanille Douce',
            'quantite' => 15,
            'seuil_alerte' => 5,
        ]);

        Bougie::factory()->create([
            'reference' => 'BOUG-TEST-002',
            'parfum' => 'Lavande',
            'nom' => 'Lavande Provence',
            'quantite' => 3, // Stock bas
            'seuil_alerte' => 5,
        ]);

        Bougie::factory()->create([
            'reference' => 'BOUG-TEST-003',
            'parfum' => 'Rupture',
            'nom' => 'Test Rupture Stock',
            'quantite' => 0, // Rupture de stock
            'seuil_alerte' => 5,
        ]);
    }
}
