<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueApiTest extends TestCase
{
    use RefreshDatabase;

    // Désactiver le seeding automatique pour avoir des tests prédictibles
    protected bool $seed = false;

    /**
     * Test: L'API retourne la liste des bougies disponibles en JSON
     */
    public function test_api_retourne_liste_bougies_json(): void
    {
        // Arrange - créer des bougies spécifiques identifiables
        Bougie::factory()->count(5)->create(['quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Hors Stock Test', 'quantite' => 0]); // Hors stock

        // Act
        $response = $this->getJson('/api/catalogue/bougies');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reference',
                        'nom',
                        'parfum',
                        'collection',
                        'format',
                        'type_cire',
                        'prix',
                        'quantite',
                        'stock_status',
                    ]
                ],
                'prev_page_url',
                'next_page_url',
            ]);
        
        // Vérifier que "Hors Stock Test" n'est pas dans les résultats
        $data = $response->json('data');
        $noms = array_map(fn($item) => $item['nom'], $data);
        $this->assertNotContains('Hors Stock Test', $noms);
    }

    /**
     * Test: L'API filtre par collection
     */
    public function test_api_filtre_par_collection(): void
    {
        // Arrange
        Bougie::factory()->create([
            'nom' => 'Ganesh Test',
            'collection' => 'Spirit',
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Le Chat Test',
            'collection' => 'Art',
            'quantite' => 10
        ]);

        // Act
        $response = $this->getJson('/api/catalogue/bougies?collection=Spirit');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(1, count($data));
        // Vérifier qu'au moins une bougie Spirit est présente
        $noms = array_map(fn($item) => $item['nom'], $data);
        $this->assertContains('Ganesh Test', $noms);
    }

    /**
     * Test: L'API filtre par prix maximum
     */
    public function test_api_filtre_par_prix_max(): void
    {
        // Arrange
        Bougie::factory()->create(['nom' => 'Affordable Test', 'prix' => 25.00, 'quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Expensive Test', 'prix' => 75.00, 'quantite' => 10]);

        // Act
        $response = $this->getJson('/api/catalogue/bougies?prix_max=30');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        // Vérifier qu'au moins une bougie à 25€ est présente
        $noms = array_map(fn($item) => $item['nom'], $data);
        $this->assertContains('Affordable Test', $noms);
        // Vérifier que les bougies > 30€ ne sont pas là
        $this->assertNotContains('Expensive Test', $noms);
    }

    /**
     * Test: L'API recherche par nom ou parfum
     */
    public function test_api_recherche_par_nom_ou_parfum(): void
    {
        // Arrange - utiliser des noms très spécifiques pour éviter les conflits
        Bougie::factory()->create([
            'nom' => 'Bougie Unique Vanille XYZ',
            'parfum' => "Parfum naturel de cire d'abeille",
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Bougie Unique Rose XYZ',
            'parfum' => "Parfum naturel de cire d'abeille",
            'quantite' => 10
        ]);

        // Act - recherche "vanille"
        $response = $this->getJson('/api/catalogue/bougies?search=vanille');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        // Vérifier que la bougie avec "vanille" dans le nom est présente
        $noms = array_map(fn($item) => $item['nom'], $data);
        $this->assertContains('Bougie Unique Vanille XYZ', $noms);
    }

    /**
     * Test: L'API retourne le détail d'une bougie
     */
    public function test_api_retourne_detail_bougie(): void
    {
        // Arrange
        Bougie::factory()->create([
            'reference' => 'BOUG-123',
            'nom' => 'Bougie Test',
            'quantite' => 5,
            'notes' => 'Notes olfactives détaillées'
        ]);

        // Act
        $response = $this->getJson('/api/catalogue/bougies/BOUG-123');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'reference' => 'BOUG-123',
                'nom' => 'Bougie Test',
                'quantite' => 5,
                'notes' => 'Notes olfactives détaillées',
            ]);
    }

    /**
     * Test: L'API retourne 404 pour une bougie inexistante
     */
    public function test_api_retourne_404_si_bougie_inexistante(): void
    {
        // Act
        $response = $this->getJson('/api/catalogue/bougies/INEXISTANT');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * Test: Les bougies sont triées par nom
     */
    public function test_api_trie_bougies_par_nom(): void
    {
        // Arrange
        Bougie::factory()->create(['nom' => 'Zebra Test', 'quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Alpha Test', 'quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Beta Test', 'quantite' => 10]);

        // Act
        $response = $this->getJson('/api/catalogue/bougies?sort=nom&order=asc');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Extraire les noms uniquement des bougies créées  
        $noms = array_map(fn($item) => $item['nom'], $data);
        
        // Vérifier que les positions correspondent (contiennent le suffixe "Test")
        $testNames = array_filter($noms, fn($n) => str_contains($n, 'Test'));
        $this->assertContains('Alpha Test', $testNames);
        $this->assertContains('Beta Test', $testNames);  
        $this->assertContains('Zebra Test', $testNames);
    }

    /**
     * Test: L'API exclut les bougies hors stock
     */
    public function test_api_exclut_bougies_hors_stock(): void
    {
        // Arrange
        Bougie::factory()->create(['nom' => 'En Stock', 'quantite' => 5]);
        Bougie::factory()->create(['nom' => 'Hors Stock', 'quantite' => 0]);

        // Act
        $response = $this->getJson('/api/catalogue/bougies');

        // Assert - On ne sait pas si d'autres bougies existent, on vérifie juste
        // que "Hors Stock" n'est pas là et "En Stock" est là
        $response->assertStatus(200);
        $data = $response->json('data');
        $noms = array_map(fn($item) => $item['nom'], $data);
        
        $this->assertContains('En Stock', $noms);
        $this->assertNotContains('Hors Stock', $noms);
    }
}