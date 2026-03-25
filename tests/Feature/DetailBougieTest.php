<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetailBougieTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: L'API retourne les détails d'une bougie spécifique
     */
    public function test_api_retourne_details_bougie()
    {
        // Arrange: Créer une bougie
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'parfum' => 'Vanille',
            'collection' => 'Hiver',
            'format' => '200g',
            'type_cire' => 'Soja',
            'prix' => 25.00,
            'temps_brulure' => 45,
            'notes' => 'Notes de vanille bourbon et caramel',
            'quantite' => 10,
        ]);

        // Act: Appeler l'API de détail
        $response = $this->getJson('/api/bougies/' . $bougie->id);

        // Assert: Vérifier la réponse complète
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $bougie->id,
                'reference' => 'BOUG-001',
                'nom' => 'Bougie Vanille',
                'parfum' => 'Vanille',
                'collection' => 'Hiver',
                'format' => '200g',
                'type_cire' => 'Soja',
                'prix' => '25.00',
                'temps_brulure' => 45,
                'notes' => 'Notes de vanille bourbon et caramel',
                'quantite' => 10,
            ]);
    }

    /**
     * Test: L'API retourne 404 pour une bougie inexistante
     */
    public function test_api_retourne_404_bougie_inexistante()
    {
        // Act
        $response = $this->getJson('/api/bougies/99999');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * Test: La page de détail est accessible
     */
    public function test_page_detail_est_accessible()
    {
        // Arrange - Utiliser stockOk pour avoir quantite > 0
        $bougie = Bougie::factory()->stockOk()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
        ]);

        // Act
        $response = $this->get('/catalogue/' . $bougie->id);

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('catalogue.show');
    }

    /**
     * Test: La page de détail injecte la bougie dans Vue
     */
    public function test_page_detail_injecte_bougie_dans_vue()
    {
        // Arrange - Utiliser stockOk pour avoir quantite > 0
        $bougie = Bougie::factory()->stockOk()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Vanille',
            'parfum' => 'Vanille',
        ]);

        // Act
        $response = $this->get('/catalogue/' . $bougie->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('BOUG-001')
            ->assertSee('Bougie Vanille');
    }

    /**
     * Test: L'API retourne des bougies similaires (même parfum)
     */
    public function test_api_retourne_bougies_similaires_meme_parfum()
    {
        // Arrange
        $bougie1 = Bougie::factory()->create([
            'parfum' => 'Vanille',
            'nom' => 'Bougie Vanille 1',
            'quantite' => 10,
        ]);
        
        $bougie2 = Bougie::factory()->create([
            'parfum' => 'Vanille',
            'nom' => 'Bougie Vanille 2',
            'quantite' => 8,
        ]);

        $bougie3 = Bougie::factory()->create([
            'parfum' => 'Lavande',
            'nom' => 'Bougie Lavande',
            'quantite' => 10,
        ]);

        // Act
        $response = $this->getJson('/api/bougies/' . $bougie1->id . '/similaires');

        // Assert: Doit retourner bougie2 mais pas bougie3
        $response->assertStatus(200)
            ->assertJsonFragment(['nom' => 'Bougie Vanille 2'])
            ->assertJsonMissing(['nom' => 'Bougie Lavande']);
        
        // Vérifier qu'on a au moins 1 résultat (pas exact car les seeders créent des bougies)
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(1, count($data));
    }
}