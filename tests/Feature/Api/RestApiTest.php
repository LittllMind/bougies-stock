<?php

namespace Tests\Feature\Api;

use App\Models\Bougie;
use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Tests API REST Publique - T5.4
 * 
 * Endpoints:
 * - GET /api/bougies (liste avec filtres)
 * - GET /api/bougies/{id} (détail)
 * - GET /api/categories (liste catégories)
 * - Rate limiting: 60 req/min
 */
class RestApiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = false;

    // ============================================================================
    // TESTS: GET /api/bougies - Liste avec filtres
    // ============================================================================

    /**
     * Test: GET /api/bougies retourne liste JSON avec structure correcte
     */
    public function test_api_bougies_liste_retourne_json(): void
    {
        // Arrange
        Bougie::factory()->count(3)->create(['quantite' => 10]);

        // Act
        $response = $this->getJson('/api/bougies');

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
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ]
            ]);
    }

    /**
     * Test: Filtre par collection
     */
    public function test_api_bougies_filtre_collection(): void
    {
        Bougie::factory()->create([
            'nom' => 'Ganesh API',
            'collection' => 'Spirit',
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Chat API',
            'collection' => 'Art',
            'quantite' => 10
        ]);

        $response = $this->getJson('/api/bougies?collection=Spirit');

        $response->assertStatus(200);
        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertContains('Ganesh API', $noms);
        $this->assertNotContains('Chat API', $noms);
    }

    /**
     * Test: Filtre par prix min/max
     */
    public function test_api_bougies_filtre_prix(): void
    {
        Bougie::factory()->create([
            'nom' => 'Pas Cher API',
            'prix' => 20.00,
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Cher API',
            'prix' => 80.00,
            'quantite' => 10
        ]);

        $response = $this->getJson('/api/bougies?prix_min=50&prix_max=100');

        $response->assertStatus(200);
        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertNotContains('Pas Cher API', $noms);
        $this->assertContains('Cher API', $noms);
    }

    /**
     * Test: Recherche par nom ou parfum
     */
    public function test_api_bougies_recherche(): void
    {
        Bougie::factory()->create([
            'nom' => 'Bougie Vanille T5.4',
            'parfum' => 'Vanille',
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Bougie Rose T5.4',
            'parfum' => 'Rose',
            'quantite' => 10
        ]);

        $response = $this->getJson('/api/bougies?search=vanille');

        $response->assertStatus(200);
        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertContains('Bougie Vanille T5.4', $noms);
        $this->assertNotContains('Bougie Rose T5.4', $noms);
    }

    /**
     * Test: Tri par nom, prix, stock
     */
    public function test_api_bougies_tri(): void
    {
        Bougie::factory()->create(['nom' => 'Zebra', 'prix' => 50, 'quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Alpha', 'prix' => 50, 'quantite' => 10]);

        $response = $this->getJson('/api/bougies?sort=nom&order=asc');

        $response->assertStatus(200);
        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertTrue(
            array_search('Alpha', $noms) < array_search('Zebra', $noms),
            'Les bougies devraient être triées par nom ASC'
        );
    }

    /**
     * Test: Pagination par défaut (15 items)
     */
    public function test_api_bougies_pagination_par_defaut(): void
    {
        Bougie::factory()->count(20)->create(['quantite' => 10]);

        $response = $this->getJson('/api/bougies');

        $response->assertStatus(200);
        $this->assertCount(15, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    /**
     * Test: Pagination customisée (per_page)
     */
    public function test_api_bougies_pagination_custom(): void
    {
        Bougie::factory()->count(25)->create(['quantite' => 10]);

        $response = $this->getJson('/api/bougies?per_page=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(10, $response->json('meta.per_page'));
    }

    /**
     * Test: Exclut les bougies hors stock
     */
    public function test_api_bougies_exclut_hors_stock(): void
    {
        Bougie::factory()->create(['nom' => 'En Stock', 'quantite' => 5]);
        Bougie::factory()->create(['nom' => 'Hors Stock', 'quantite' => 0]);

        $response = $this->getJson('/api/bougies');

        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertContains('En Stock', $noms);
        $this->assertNotContains('Hors Stock', $noms);
    }

    // ============================================================================
    // TESTS: GET /api/bougies/{id} - Détail
    // ============================================================================

    /**
     * Test: GET /api/bougies/{id} retourne détail complet
     */
    public function test_api_bougies_detail_retourne_json(): void
    {
        $bougie = Bougie::factory()->create([
            'nom' => 'Detail Test',
            'reference' => 'BOUG-DETAIL-001',
            'quantite' => 15,
            'notes' => 'Notes détaillées'
        ]);

        $response = $this->getJson("/api/bougies/{$bougie->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $bougie->id,
                    'nom' => 'Detail Test',
                    'reference' => 'BOUG-DETAIL-001',
                    'quantite' => 15,
                    'notes' => 'Notes détaillées',
                ]
            ]);
    }

    /**
     * Test: GET /api/bougies/{id} retourne 404 si inexistant
     */
    public function test_api_bougies_detail_404_si_inexistant(): void
    {
        $response = $this->getJson('/api/bougies/99999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Bougie non trouvée']);
    }

    /**
     * Test: GET /api/bougies/{reference} aussi fonctionnel
     */
    public function test_api_bougies_detail_par_reference(): void
    {
        $bougie = Bougie::factory()->create([
            'nom' => 'Par Reference',
            'reference' => 'BOUG-REF-123',
            'quantite' => 10
        ]);

        $response = $this->getJson('/api/bougies/BOUG-REF-123');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'reference' => 'BOUG-REF-123',
                    'nom' => 'Par Reference',
                ]
            ]);
    }

    // ============================================================================
    // TESTS: GET /api/categories - Liste catégories
    // ============================================================================

    /**
     * Test: GET /api/categories retourne liste des collections
     */
    public function test_api_categories_liste(): void
    {
        Bougie::factory()->create(['collection' => 'Spirit', 'quantite' => 10]);
        Bougie::factory()->create(['collection' => 'Art', 'quantite' => 10]);
        Bougie::factory()->create(['collection' => 'Nature', 'quantite' => 10]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'count',
                    ]
                ]
            ]);

        $collections = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('Spirit', $collections);
        $this->assertContains('Art', $collections);
        $this->assertContains('Nature', $collections);
    }

    /**
     * Test: Catégories avec count correct
     */
    public function test_api_categories_count_correct(): void
    {
        Bougie::factory()->count(3)->create(['collection' => 'Spirit', 'quantite' => 10]);
        Bougie::factory()->count(2)->create(['collection' => 'Art', 'quantite' => 10]);
        Bougie::factory()->create(['collection' => 'Hors Stock', 'quantite' => 0]); // Exclu

        $response = $this->getJson('/api/categories');

        $data = collect($response->json('data'))->keyBy('name');
        $this->assertEquals(3, $data['Spirit']['count'] ?? 0);
        $this->assertEquals(2, $data['Art']['count'] ?? 0);
        $this->assertNull($data['Hors Stock'] ?? null);
    }

    // ============================================================================
    // TESTS: Rate Limiting (60 req/min)
    // ============================================================================

    /**
     * Test: Rate limit 60 req/min - Accepte requests sous le seuil
     */
    public function test_api_rate_limit_accepter_sous_seuil(): void
    {
        RateLimiter::clear('api');

        Bougie::factory()->create(['quantite' => 10]);

        // 5 requêtes OK
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/bougies');
            $response->assertStatus(200);
        }
    }

    /**
     * Test: Rate limit retourne 429 après 60 req/min
     * 
     * @slowThreshold 2000
     */
    public function test_api_rate_limit_429_apres_60_req(): void
    {
        RateLimiter::clear('api');

        Bougie::factory()->create(['quantite' => 10]);

        // 61 requêtes rapides
        for ($i = 0; $i < 61; $i++) {
            $response = $this->getJson('/api/bougies');
        }

        // La dernière doit être 429
        $response->assertStatus(429)
            ->assertJson(['message' => 'Trop de requêtes. Veuillez réessayer plus tard.']);
    }

    /**
     * Test: Headers rate limit présents
     */
    public function test_api_rate_limit_headers(): void
    {
        RateLimiter::clear('api');

        Bougie::factory()->create(['quantite' => 10]);

        $response = $this->getJson('/api/bougies');

        $response->assertStatus(200);
        $this->assertTrue(
            $response->headers->has('X-RateLimit-Limit') || 
            $response->headers->has('X-Ratelimit-Limit'),
            'Header X-RateLimit-Limit devrait être présent'
        );
    }

    // ============================================================================
    // TESTS: Headers CORS et Content-Type
    // ============================================================================

    /**
     * Test: API retourne Content-Type application/json
     */
    public function test_api_content_type_json(): void
    {
        Bougie::factory()->create(['quantite' => 10]);

        $response = $this->get('/api/bougies');

        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Test: API accepte header Accept: application/json
     */
    public function test_api_accept_json_header(): void
    {
        Bougie::factory()->create(['quantite' => 10]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/bougies');

        $response->assertStatus(200);
    }

    // ============================================================================
    // TESTS: Combinaison filtres
    // ============================================================================

    /**
     * Test: Combinaison filtres collection + prix
     */
    public function test_api_filtres_combines_collection_prix(): void
    {
        Bougie::factory()->create([
            'nom' => 'Spirit Cher',
            'collection' => 'Spirit',
            'prix' => 80.00,
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Spirit Pas Cher',
            'collection' => 'Spirit',
            'prix' => 20.00,
            'quantite' => 10
        ]);
        Bougie::factory()->create([
            'nom' => 'Art Cher',
            'collection' => 'Art',
            'prix' => 80.00,
            'quantite' => 10
        ]);

        $response = $this->getJson('/api/bougies?collection=Spirit&prix_min=50');

        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertContains('Spirit Cher', $noms);
        $this->assertNotContains('Spirit Pas Cher', $noms);
        $this->assertNotContains('Art Cher', $noms);
    }

    /**
     * Test: Filtres avec search + tri
     */
    public function test_api_filtres_combines_search_tri(): void
    {
        Bougie::factory()->create(['nom' => 'Bougie Zen', 'collection' => 'Spirit', 'quantite' => 10]);
        Bougie::factory()->create(['nom' => 'Bougie Art', 'collection' => 'Art', 'quantite' => 10]);

        $response = $this->getJson('/api/bougies?collection=Spirit&sort=nom');

        $response->assertStatus(200);
        $noms = collect($response->json('data'))->pluck('nom')->toArray();
        $this->assertContains('Bougie Zen', $noms);
        $this->assertNotContains('Bougie Art', $noms);
    }
}
