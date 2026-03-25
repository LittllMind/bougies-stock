<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BougieDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_retourne_detail_bougie_par_reference()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-001',
            'nom' => 'Rose Élégante',
            'parfum' => 'Rose',
            'collection' => 'Florale',
            'format' => '180g',
            'type_cire' => 'Soja',
            'temps_brulure' => 40,
            'prix' => 24.90,
            'quantite' => 15,
        ]);

        $response = $this->getJson('/api/bougies/BOUG-001');

        $response->assertStatus(200)
            ->assertJsonPath('data.reference', 'BOUG-001')
            ->assertJsonPath('data.nom', 'Rose Élégante')
            ->assertJsonPath('data.parfum', 'Rose')
            ->assertJsonPath('data.collection', 'Florale')
            ->assertJsonPath('data.format', '180g')
            ->assertJsonPath('data.type_cire', 'Soja')
            ->assertJsonPath('data.temps_brulure', 40)
            ->assertJsonPath('data.quantite', 15);
        
        // Vérifier que le prix est dans la réponse (comparaison float flexible)
        $json = $response->json();
        $this->assertTrue(
            abs($json['data']['prix'] - 24.90) < 0.01,
            "Le prix devrait être proche de 24.90"
        );
    }

    public function test_api_retourne_404_si_bougie_inexistante()
    {
        $response = $this->getJson('/api/bougies/REFERENCE-INEXISTANTE');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Bougie non trouvée']);
    }

    public function test_page_detail_affiche_vuejs()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-002',
            'nom' => 'Vanille Douce',
            'quantite' => 5,
        ]);

        $response = $this->get('/catalogue/BOUG-002');

        $response->assertStatus(200)
            ->assertSee('id="app-detail"', false)
            ->assertSee('BOUG-002', false);
    }

    public function test_vue_affiche_toutes_les_donnees_bougie()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-003',
            'nom' => 'Lavande Relaxante',
            'parfum' => 'Lavande',
            'collection' => 'Bien-être',
            'format' => '200g',
            'type_cire' => 'Cire de soja',
            'temps_brulure' => 45,
            'notes' => 'Notes de tête: lavande, Notes de cœur: camomille',
            'prix' => 28.50,
            'quantite' => 8,
        ]);

        // On récupère la bougie depuis l'API pour tester
        $response = $this->getJson('/api/bougies/BOUG-003');

        $response->assertStatus(200)
            ->assertJsonPath('data.reference', 'BOUG-003')
            ->assertJsonPath('data.nom', 'Lavande Relaxante')
            ->assertJsonPath('data.parfum', 'Lavande')
            ->assertJsonPath('data.collection', 'Bien-être')
            ->assertJsonPath('data.format', '200g')
            ->assertJsonPath('data.type_cire', 'Cire de soja')
            ->assertJsonPath('data.temps_brulure', 45)
            ->assertJsonPath('data.notes', 'Notes de tête: lavande, Notes de cœur: camomille')
            ->assertJsonPath('data.quantite', 8);
        
        // Vérification du prix (comparaison float)
        $json = $response->json();
        $this->assertTrue(
            abs($json['data']['prix'] - 28.50) < 0.01,
            "Le prix devrait être proche de 28.50"
        );
    }

    public function test_quantite_stock_disponible_dans_reponse()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-004',
            'quantite' => 3,
        ]);

        $response = $this->getJson('/api/bougies/BOUG-004');

        $response->assertStatus(200)
            ->assertJsonPath('data.quantite', 3);
    }

    public function test_page_detail_retourne_404_si_reference_invalide()
    {
        $response = $this->get('/catalogue/INVALIDE-REF');

        $response->assertStatus(404);
    }

    public function test_page_detail_retourne_404_si_bougie_hors_stock()
    {
        $bougie = Bougie::factory()->create([
            'reference' => 'BOUG-EPUISE',
            'quantite' => 0,
        ]);

        $response = $this->get('/catalogue/BOUG-EPUISE');

        $response->assertStatus(404);
    }
}
