<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KiosqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosque_affiche_bougies_en_stock()
    {
        // Créer des bougies avec stock
        Bougie::factory()->count(3)->create([
            'quantite' => 10,
            'prix' => 25,
            'collection' => 'Spirit',
        ]);

        // Accéder au kiosque
        $response = $this->get('/kiosque');
        
        // Vérifier que la page charge
        $response->assertStatus(200);
        
        // Vérifier qu'on voit les bougies
        $response->assertSee('Nos Bougies Artisanales');
    }

    public function test_kiosque_ne_montre_pas_bougies_sans_stock()
    {
        // Créer bougie sans stock
        Bougie::factory()->create([
            'quantite' => 0,
            'prix' => 25,
        ]);

        // Accéder au kiosque
        $response = $this->get('/kiosque');
        
        $response->assertStatus(200);
        $response->assertSee('Aucune bougie');
    }
}
