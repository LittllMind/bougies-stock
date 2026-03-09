<?php

namespace Tests\Feature\Vinyles;

use App\Models\Vinyle;
use App\Models\Fond;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VinyleControllerActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->adminUser();
    }

    /** @test */
    public function admin_peut_voir_formulaire_creation(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('vinyles.create'));

        $response->assertOk()
            ->assertViewIs('vinyles.form')
            ->assertViewHas('vinyle');
    }

    /** @test */
    public function employe_peut_voir_formulaire_creation(): void
    {
        $employe = $this->employeUser();

        $response = $this->actingAs($employe)
            ->get(route('vinyles.create'));

        $response->assertOk()
            ->assertViewIs('vinyles.form');
    }

    /** @test */
    public function client_ne_peut_pas_voir_formulaire_creation(): void
    {
        $client = $this->clientUser();

        $response = $this->actingAs($client)
            ->get(route('vinyles.create'));

        // Client n'a pas le rôle admin/employe, redirection au lieu de dashboard
        $response->assertRedirect();
    }

    /** @test */
    public function admin_peut_creer_vinyle(): void
    {
        $fond = Fond::factory()->create(['quantite' => 10]);

        $response = $this->actingAs($this->admin)
            ->post(route('vinyles.store'), [
                'nom' => 'Test Album',
                'modele' => 'Standard',
                'artiste' => 'Test Artist',
                'prix' => 25.50,
                'quantite' => 5,
                'fond_id' => $fond->id,
            ]);

        $response->assertRedirect(route('vinyles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vinyles', [
            'nom' => 'Test Album',
            'modele' => 'Standard',
            'artiste' => 'Test Artist',
            'prix' => 25.50,
            'quantite' => 5,
        ]);
    }

    /** @test */
    public function employe_peut_creer_vinyle(): void
    {
        $employe = $this->employeUser();

        $response = $this->actingAs($employe)
            ->post(route('vinyles.store'), [
                'nom' => 'Album Employe',
                'modele' => 'Deluxe',
                'prix' => 30.00,
                'quantite' => 3,
            ]);

        $response->assertRedirect(route('vinyles.index'));

        $this->assertDatabaseHas('vinyles', [
            'nom' => 'Album Employe',
        ]);
    }

    /** @test */
    public function client_ne_peut_pas_creer_vinyle(): void
    {
        $client = $this->clientUser();

        $response = $this->actingAs($client)
            ->post(route('vinyles.store'), [
                'nom' => 'Hacked Album',
                'modele' => 'Standard',
                'prix' => 1.00,
                'quantite' => 100,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('vinyles', [
            'nom' => 'Hacked Album',
        ]);
    }

    /** @test */
    public function validation_rejecte_nom_vide(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('vinyles.store'), [
                'nom' => '',
                'modele' => 'Standard',
                'prix' => 25.00,
                'quantite' => 5,
            ]);

        $response->assertSessionHasErrors('nom');
    }

    /** @test */
    public function validation_rejecte_prix_negatif(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('vinyles.store'), [
                'nom' => 'Test Album',
                'modele' => 'Standard',
                'prix' => -10.00,
                'quantite' => 5,
            ]);

        $response->assertSessionHasErrors('prix');
    }

    /** @test */
    public function validation_rejecte_quantite_negative(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('vinyles.store'), [
                'nom' => 'Test Album',
                'modele' => 'Standard',
                'prix' => 25.00,
                'quantite' => -5,
            ]);

        $response->assertSessionHasErrors('quantite');
    }

    /** @test */
    public function admin_peut_modifier_vinyle(): void
    {
        $vinyle = Vinyle::factory()->create([
            'nom' => 'Old Name',
            'prix' => 20.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('vinyles.update', $vinyle), [
                'nom' => 'New Name',
                'modele' => $vinyle->modele,
                'prix' => 35.00,
                'quantite' => 10,
            ]);

        $response->assertRedirect(route('vinyles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vinyles', [
            'id' => $vinyle->id,
            'nom' => 'New Name',
            'prix' => 35.00,
        ]);
    }

    /** @test */
    public function employe_peut_modifier_vinyle(): void
    {
        $employe = $this->employeUser();
        $vinyle = Vinyle::factory()->create(['nom' => 'Original']);

        $response = $this->actingAs($employe)
            ->patch(route('vinyles.update', $vinyle), [
                'nom' => 'Modified by Employe',
                'modele' => $vinyle->modele,
                'prix' => $vinyle->prix,
                'quantite' => $vinyle->quantite,
            ]);

        $response->assertRedirect(route('vinyles.index'));

        $this->assertDatabaseHas('vinyles', [
            'id' => $vinyle->id,
            'nom' => 'Modified by Employe',
        ]);
    }

    /** @test */
    public function client_ne_peut_pas_modifier_vinyle(): void
    {
        $client = $this->clientUser();
        $vinyle = Vinyle::factory()->create(['nom' => 'Protected']);

        $response = $this->actingAs($client)
            ->patch(route('vinyles.update', $vinyle), [
                'nom' => 'Hacked',
                'modele' => $vinyle->modele,
                'prix' => $vinyle->prix,
                'quantite' => $vinyle->quantite,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('vinyles', [
            'id' => $vinyle->id,
            'nom' => 'Protected',
        ]);
    }

    /** @test */
    public function admin_peut_supprimer_vinyle(): void
    {
        $vinyle = Vinyle::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('vinyles.destroy', $vinyle));

        $response->assertRedirect(route('vinyles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('vinyles', [
            'id' => $vinyle->id,
        ]);
    }

    /** @test */
    public function employe_peut_supprimer_vinyle(): void
    {
        $employe = $this->employeUser();
        $vinyle = Vinyle::factory()->create();

        $response = $this->actingAs($employe)
            ->delete(route('vinyles.destroy', $vinyle));

        $response->assertRedirect(route('vinyles.index'));

        $this->assertDatabaseMissing('vinyles', [
            'id' => $vinyle->id,
        ]);
    }

    /** @test */
    public function client_ne_peut_pas_supprimer_vinyle(): void
    {
        $client = $this->clientUser();
        $vinyle = Vinyle::factory()->create();

        $response = $this->actingAs($client)
            ->delete(route('vinyles.destroy', $vinyle));

        $response->assertRedirect();

        $this->assertDatabaseHas('vinyles', [
            'id' => $vinyle->id,
        ]);
    }

    /** @test */
    public function suppression_vinyle_inexistant_retourne_404(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('vinyles.destroy', 99999));

        $response->assertNotFound();
    }

    /** @test */
    public function modification_vinyle_inexistant_retourne_404(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('vinyles.update', 99999), [
                'nom' => 'Test',
                'modele' => 'Standard',
                'prix' => 25.00,
                'quantite' => 5,
            ]);

        $response->assertNotFound();
    }

    /** @test */
    public function guest_est_redirige_vers_login_pour_actions(): void
    {
        $vinyle = Vinyle::factory()->create();

        // Create
        $response = $this->get(route('vinyles.create'));
        $response->assertRedirect(route('login'));

        // Store
        $response = $this->post(route('vinyles.store'), []);
        $response->assertRedirect(route('login'));

        // Edit
        $response = $this->get(route('vinyles.edit', $vinyle));
        $response->assertRedirect(route('login'));

        // Update
        $response = $this->patch(route('vinyles.update', $vinyle), []);
        $response->assertRedirect(route('login'));

        // Destroy
        $response = $this->delete(route('vinyles.destroy', $vinyle));
        $response->assertRedirect(route('login'));
    }
}
