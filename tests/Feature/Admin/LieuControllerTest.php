<?php

namespace Tests\Feature\Admin;

use App\Models\Lieu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LieuControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /**
     * T1.2: Admin peut voir la liste des lieux
     */
    public function test_admin_peut_voir_liste_lieux(): void
    {
        Lieu::factory()->count(3)->create();
        
        $response = $this->actingAs($this->admin)
            ->get('/admin/lieux');
        
        $response->assertStatus(200);
        $response->assertViewHas('lieux');
    }

    /**
     * T1.2: Non-admin ne peut pas voir la liste des lieux
     */
    public function test_non_admin_ne_peut_pas_voir_liste_lieux(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)
            ->get('/admin/lieux');
        
        $response->assertStatus(403);
    }

    /**
     * T1.2: Invité ne peut pas voir la liste des lieux
     */
    public function test_invite_ne_peut_pas_voir_liste_lieux(): void
    {
        $response = $this->get('/admin/lieux');
        
        $response->assertRedirect('/login');
    }

    /**
     * T1.3: Admin peut créer un lieu
     */
    public function test_admin_peut_creer_lieu(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/lieux', [
                'nom' => 'Nouveau',
                'adresse' => '123 Rue',
                'ville' => 'Ville',
                'code_postal' => '12345',
                'actif' => true,
            ]);
        
        $response->assertRedirect('/admin/lieux');
        $this->assertDatabaseHas('lieux', ['nom' => 'Nouveau']);
    }

    /**
     * T1.3: Non-admin ne peut pas créer un lieu
     */
    public function test_non_admin_ne_peut_pas_creer_lieu(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)
            ->post('/admin/lieux', [
                'nom' => 'Nouveau',
                'adresse' => '123 Rue',
                'ville' => 'Ville',
                'code_postal' => '12345',
            ]);
        
        $response->assertStatus(403);
    }

    /**
     * T1.3: Création avec validation échoue si champs requis manquants
     */
    public function test_creer_lieu_echoue_si_champs_requis_manquants(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/lieux', [
                'nom' => '',
                'adresse' => '',
            ]);
        
        $response->assertSessionHasErrors(['nom', 'adresse', 'ville', 'code_postal']);
    }

    /**
     * T1.3: Admin peut voir le formulaire de création
     */
    public function test_admin_peut_voir_formulaire_creation(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/lieux/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.lieux.create');
    }
}
