<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Bougie;
use App\Models\User;

class BougieControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /**
     * Test: L'admin peut voir la liste des bougies
     * Route: GET /admin/bougies
     */
    public function test_admin_peut_voir_liste_bougies()
    {
        // Créer quelques bougies
        Bougie::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/bougies');

        $response->assertStatus(200)
            ->assertViewIs('admin.bougies.index')
            ->assertViewHas('bougies');
    }

    /**
     * Test: L'admin peut voir le formulaire de création
     * Route: GET /admin/bougies/create
     */
    public function test_admin_peut_voir_formulaire_creation()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/bougies/create');

        $response->assertStatus(200)
            ->assertViewIs('admin.bougies.create');
    }

    /**
     * Test: L'admin peut créer une nouvelle bougie
     * Route: POST /admin/bougies
     */
    public function test_admin_peut_creer_bougie()
    {
        $donnees = [
            'reference' => 'BOUG-TEST-001',
            'parfum' => 'Vanille',
            'nom' => 'Bougie Vanille',
            'collection' => 'classique',
            'format' => '200g',
            'type_cire' => 'soja',
            'temps_brulure' => 40,
            'notes' => 'Notes de vanille douce',
            'prix' => 24.99,
            'quantite' => 10,
            'seuil_alerte' => 5,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/bougies', $donnees);

        $response->assertRedirect(route('admin.bougies.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bougies', [
            'reference' => 'BOUG-TEST-001',
            'nom' => 'Bougie Vanille',
        ]);
    }

    /**
     * Test: L'admin peut voir les détails d'une bougie
     * Route: GET /admin/bougies/{bougie}
     */
    public function test_admin_peut_voir_details_bougie()
    {
        $bougie = Bougie::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/bougies/{$bougie->id}");

        $response->assertStatus(200)
            ->assertViewIs('admin.bougies.show')
            ->assertViewHas('bougie');
    }

    /**
     * Test: L'admin peut voir le formulaire d'édition
     * Route: GET /admin/bougies/{bougie}/edit
     */
    public function test_admin_peut_voir_formulaire_edition()
    {
        $bougie = Bougie::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/bougies/{$bougie->id}/edit");

        $response->assertStatus(200)
            ->assertViewIs('admin.bougies.edit')
            ->assertViewHas('bougie');
    }

    /**
     * Test: L'admin peut modifier une bougie
     * Route: PUT /admin/bougies/{bougie}
     */
    public function test_admin_peut_modifier_bougie()
    {
        $bougie = Bougie::factory()->create([
            'nom' => 'Ancien Nom',
            'prix' => 20.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->put("/admin/bougies/{$bougie->id}", [
                'reference' => $bougie->reference,
                'parfum' => $bougie->parfum,
                'nom' => 'Nouveau Nom',
                'collection' => $bougie->collection,
                'format' => $bougie->format,
                'type_cire' => $bougie->type_cire,
                'temps_brulure' => $bougie->temps_brulure,
                'notes' => $bougie->notes,
                'prix' => 29.99,
                'quantite' => $bougie->quantite,
                'seuil_alerte' => $bougie->seuil_alerte,
            ]);

        $response->assertRedirect(route('admin.bougies.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bougies', [
            'id' => $bougie->id,
            'nom' => 'Nouveau Nom',
            'prix' => 29.99,
        ]);
    }

    /**
     * Test: L'admin peut supprimer une bougie
     * Route: DELETE /admin/bougies/{bougie}
     */
    public function test_admin_peut_supprimer_bougie()
    {
        $bougie = Bougie::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/bougies/{$bougie->id}");

        $response->assertRedirect(route('admin.bougies.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('bougies', [
            'id' => $bougie->id,
        ]);
    }

    /**
     * Test: Les champs requis sont validés lors de la création
     * Route: POST /admin/bougies
     */
    public function test_validation_requise_pour_creation()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/bougies', []);

        $response->assertSessionHasErrors([
            'reference',
            'parfum',
            'nom',
            'prix',
        ]);
    }

    /**
     * Test: La référence doit être unique
     * Route: POST /admin/bougies
     */
    public function test_reference_doit_etre_unique()
    {
        Bougie::factory()->create(['reference' => 'BOUG-UNIQUE']);

        $response = $this->actingAs($this->admin)
            ->post('/admin/bougies', [
                'reference' => 'BOUG-UNIQUE',
                'parfum' => 'Nouveau',
                'nom' => 'Nouveau',
                'prix' => 25.00,
            ]);

        $response->assertSessionHasErrors('reference');
    }
}
