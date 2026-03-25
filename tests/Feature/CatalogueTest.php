<?php

namespace Tests\Feature;

use App\Models\Bougie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: L'API retourne la liste des bougies pour le catalogue public
     */
    public function test_api_retourne_liste_bougies_pour_catalogue()
    {
        // Arrange: Créer des bougies spécifiques
        $bougie1 = Bougie::factory()->create([
            'reference' => 'CATA-001',
            'nom' => 'Bougie Vanille',
            'parfum' => 'Vanille',
            'prix' => 25.00,
            'quantite' => 10,
        ]);
        
        $bougie2 = Bougie::factory()->create([
            'reference' => 'CATA-002',
            'nom' => 'Bougie Lavande',
            'parfum' => 'Lavande',
            'prix' => 30.00,
            'quantite' => 5,
        ]);

        // Act: Appeler l'API sans authentification (public)
        $response = $this->getJson('/api/bougies');

        // Assert: Vérifier que nos bougies sont présentes (pas de count exact car peut avoir du seed)
        $response->assertStatus(200)
            ->assertJsonFragment([
                'reference' => 'CATA-001',
                'nom' => 'Bougie Vanille',
                'parfum' => 'Vanille',
                'prix' => '25.00',
            ])
            ->assertJsonFragment([
                'reference' => 'CATA-002',
                'nom' => 'Bougie Lavande',
                'parfum' => 'Lavande',
                'prix' => '30.00',
            ]);

        // Vérifier qu'on a au moins 2 résultats
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(2, count($data));
    }

    /**
     * Test: L'API retourne uniquement les bougies en stock
     */
    public function test_api_retourne_uniquement_bougies_en_stock()
    {
        // Arrange: Une bougie en stock, une rupture (référence unique pour éviter conflits)
        Bougie::factory()->create([
            'reference' => 'CATA-003',
            'nom' => 'En Stock',
            'quantite' => 10,
        ]);
        
        Bougie::factory()->create([
            'reference' => 'CATA-004',
            'nom' => 'Rupture',
            'quantite' => 0,
        ]);

        // Act
        $response = $this->getJson('/api/bougies');

        // Assert: CATA-003 doit être présent, CATA-004 absent
        $response->assertStatus(200);
        $data = $response->json('data');
        $references = array_column($data, 'reference');
        
        $this->assertContains('CATA-003', $references);
        $this->assertNotContains('CATA-004', $references);
    }

    /**
     * Test: L'API supporte le filtre par parfum
     */
    public function test_api_filtre_par_parfum()
    {
        // Arrange
        Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'parfum' => 'Vanille',
            'quantite' => 10,
        ]);
        
        Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'parfum' => 'Lavande',
            'quantite' => 10,
        ]);

        // Act
        $response = $this->getJson('/api/bougies?parfum=Vanille');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('BOUG-001', $data[0]['reference']);
    }

    /**
     * Test: L'API supporte le filtre par collection
     */
    public function test_api_filtre_par_collection()
    {
        // Arrange
        Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'collection' => 'Hiver',
            'parfum' => 'Vanille',
            'quantite' => 10,
        ]);
        
        Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'collection' => 'Été',
            'parfum' => 'Citron',
            'quantite' => 10,
        ]);

        // Act
        $response = $this->getJson('/api/bougies?collection=Hiver');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['reference' => 'BOUG-001']);
    }

    /**
     * Test: L'API supporte le tri par prix croissant
     */
    public function test_api_trie_par_prix_croissant()
    {
        // Arrange
        Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'prix' => 35.00,
            'quantite' => 10,
        ]);
        
        Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'prix' => 25.00,
            'quantite' => 10,
        ]);

        // Act
        $response = $this->getJson('/api/bougies?sort=prix&order=asc');

        // Assert: BOUG-001 avant BOUG-002
        $data = $response->json('data');
        $this->assertEquals('BOUG-001', $data[0]['reference']);
        $this->assertEquals('BOUG-002', $data[1]['reference']);
    }

    /**
     * Test: La page catalogue est accessible
     */
    public function test_page_catalogue_est_accessible()
    {
        // Act
        $response = $this->get('/catalogue');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('catalogue.index');
    }

    /**
     * Test: La page catalogue injecte les bougies dans Vue
     */
    public function test_page_catalogue_injecte_bougies_dans_vue()
    {
        // Arrange
        Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Bougie Test',
            'parfum' => 'Vanille',
            'quantite' => 5,
        ]);

        // Act
        $response = $this->get('/catalogue');

        // Assert
        $response->assertStatus(200)
            ->assertSee('BOUG-001')
            ->assertSee('Bougie Test');
    }
}
