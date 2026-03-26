<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetailBougieTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: L'API retourne les détails d'une bougie par référence
     */
    public function test_api_retourne_details_bougie(): void
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-DETAIL-001',
            'nom' => 'Bougie Test Détail',
            'parfum' => 'Vanille',
            'collection' => 'Hiver',
            'format' => '200g',
            'type_cire' => 'Soja',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        $response = $this->getJson('/api/catalogue/bougies/' . $bougie->reference);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $bougie->id,
                'reference' => 'BOUG-DETAIL-001',
                'nom' => 'Bougie Test Détail',
            ]);
    }

    /**
     * Test: L'API retourne 404 pour une référence inexistante
     */
    public function test_api_retourne_404_bougie_inexistante(): void
    {
        $response = $this->getJson('/api/catalogue/bougies/REF-INEXISTANTE');

        $response->assertStatus(404);
    }

    /**
     * Test: La page de détail est accessible par référence
     */
    public function test_page_detail_est_accessible(): void
    {
        $bougie = Bougie::factory()->stockOk()->create([
            'reference' => 'BOUG-PAGE-001',
            'nom' => 'Bougie Page Test',
        ]);

        $response = $this->get('/catalogue/' . $bougie->reference);

        $response->assertStatus(200)
            ->assertViewIs('catalogue.show');
    }

    /**
     * Test: La page de détail affiche les informations de la bougie
     */
    public function test_page_detail_affiche_informations_bougie(): void
    {
        $bougie = Bougie::factory()->stockOk()->create([
            'reference' => 'BOUG-INFO-001',
            'nom' => 'Bougie Info Test',
            'parfum' => 'Lavande',
        ]);

        $response = $this->get('/catalogue/' . $bougie->reference);

        $response->assertStatus(200)
            ->assertSee('BOUG-INFO-001')
            ->assertSee('Bougie Info Test');
    }
}
