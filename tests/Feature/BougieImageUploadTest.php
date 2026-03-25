<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Bougie;
use App\Models\User;

class BougieImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create(['email' => 'admin@example.com']);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function test_peut_creer_bougie_avec_tous_les_champs_requis()
    {
        $response = $this->from('/admin/bougies/create')
            ->post('/admin/bougies', [
                'reference' => 'BOUG-TEST',
                'parfum' => 'Vanille Test',
                'nom' => 'Bougie Test Photo',
                'prix' => 29.99,
                'quantite' => 10,
            ]);
        
        $response->assertRedirect();
        
        $bougie = Bougie::where('reference', 'BOUG-TEST')->first();
        $this->assertNotNull($bougie);
        $this->assertEquals('Bougie Test Photo', $bougie->nom);
        $this->assertEquals('Vanille Test', $bougie->parfum);
    }

    /** @test */
    public function test_creation_sans_photo_fonctionne()
    {
        $response = $this->post('/admin/bougies', [
            'reference' => 'BOUG-NOIMG',
            'parfum' => 'Sans Image',
            'nom' => 'Bougie Sans Photo',
            'prix' => 29.99,
            'quantite' => 10,
        ]);
        
        $response->assertRedirect();
        
        $bougie = Bougie::where('reference', 'BOUG-NOIMG')->first();
        $this->assertNotNull($bougie);
        $this->assertNull($bougie->image);
    }

    /** @test */
    public function test_bougie_avec_image_stocke_le_chemin_correct()
    {
        $bougie = Bougie::create([
            'reference' => 'BOUG-PATH',
            'parfum' => 'Test',
            'nom' => 'Test',
            'prix' => 29.99,
            'quantite' => 10,
            'image' => 'bougies/mon-image.jpg',
        ]);
        
        // Verifier le chemin stocké
        $this->assertEquals('bougies/mon-image.jpg', $bougie->image);
    }

    /** @test */
    public function test_bougie_sans_image_a_image_null()
    {
        $bougie = Bougie::create([
            'reference' => 'BOUG-NOURL',
            'parfum' => 'Test',
            'nom' => 'Test',
            'prix' => 29.99,
            'quantite' => 10,
        ]);
        
        $this->assertNull($bougie->image);
    }

    /** @test */
    public function test_image_vide_affiche_placeholder_dans_liste()
    {
        $bougie = Bougie::create([
            'reference' => 'BOUG-EMPTY',
            'parfum' => 'Test',
            'nom' => 'Test Empty',
            'prix' => 29.99,
            'quantite' => 10,
            'image' => null,
        ]);
        
        $response = $this->get('/admin/bougies');
        $response->assertStatus(200);
        // Vérifier emoji bougie present
        $response->assertSee('🕯️');
    }

    /** @test */
    public function test_suppression_bougie_fonctionne()
    {
        $bougie = Bougie::create([
            'reference' => 'BOUG-DEL',
            'parfum' => 'Test',
            'nom' => 'Test',
            'prix' => 29.99,
            'quantite' => 10,
        ]);
        
        $id = $bougie->id;
        $response = $this->delete("/admin/bougies/{$id}");
        $response->assertRedirect();
        
        $this->assertDatabaseMissing('bougies', ['id' => $id]);
    }

    /** @test */
    public function test_update_bougie_fonctionne()
    {
        $bougie = Bougie::create([
            'reference' => 'BOUG-UP',
            'parfum' => 'Test',
            'nom' => 'Test',
            'prix' => 29.99,
            'quantite' => 10,
        ]);
        
        $response = $this->put("/admin/bougies/{$bougie->id}", [
            'reference' => 'BOUG-UP',
            'parfum' => 'Modifie',
            'nom' => 'Nom Modifie',
            'prix' => 35.00,
            'quantite' => 15,
        ]);
        
        $response->assertRedirect();
        
        $bougie->refresh();
        $this->assertEquals('Nom Modifie', $bougie->nom);
        $this->assertEquals(15, $bougie->quantite);
    }
}
