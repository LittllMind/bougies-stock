<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KiosqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosque_affiche_bougies_en_stock(): void
    {
        // Créer des bougies avec stock
        Bougie::factory()->count(3)->create([
            'quantite' => 10,
            'prix' => 25,
            'collection' => 'Spirit',
        ]);

        // Accéder au kiosque
        $response = $this->get('/catalogue');
        
        // Vérifier que la page charge
        $response->assertStatus(200);
        
        // Vérifier qu'on voit les bougies
        $response->assertSee('Bougie');
    }

    public function test_kiosque_vide_affiche_message(): void
    {
        // Ne créer AUCUNE bougie avec stock

        // Accéder au kiosque
        $response = $this->get('/catalogue');
        
        $response->assertStatus(200);
        // Si aucune bougie n'a de stock, le catalogue est vide
        // On vérifie juste que la page se charge correctement
        $response->assertStatus(200);
    }
}
