<?php

namespace Tests\Feature\Client;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAddressesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'client']);
    }

    /** @test */
    public function liste_adresses_est_accessible_pour_client_auth()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('addresses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('addresses.index');
    }

    /** @test */
    public function liste_adresses_affiche_adresses_utilisateur()
    {
        Address::factory()->create([
            'user_id' => $this->user->id,
            'label' => 'Maison',
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('addresses.index'));

        $response->assertStatus(200);
        $response->assertSee('Maison');
        $response->assertSee('123 Rue Test');
        $response->assertSee('Paris');
    }

    /** @test */
    public function liste_adresses_ne_montre_pas_adresses_autres_utilisateurs()
    {
        $otherUser = User::factory()->create();
        
        Address::factory()->create([
            'user_id' => $otherUser->id,
            'label' => 'Autre Adresse',
            'nom' => 'Other User',
            'email' => 'other@test.com',
            'telephone' => '0123456789',
            'adresse' => '456 Other Street',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('addresses.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Autre Adresse');
        $response->assertDontSee('456 Other Street');
    }

    /** @test */
    public function creation_adresse_fonctionne()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('addresses.store'), [
                             'label' => 'Nouvelle Adresse',
                             'nom' => 'Test User',
                             'email' => 'test@test.com',
                             'telephone' => '0123456789',
                             'adresse' => '789 New Street',
                             'code_postal' => '33000',
                             'ville' => 'Bordeaux',
                             'pays' => 'FR',
                         ]);

        $response->assertRedirect(route('addresses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'label' => 'Nouvelle Adresse',
            'adresse' => '789 New Street',
        ]);
    }

    /** @test */
    public function modification_adresse_fonctionne()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'label' => 'Ancien Label',
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Old Street',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->put(route('addresses.update', $address->id), [
                             'label' => 'Nouveau Label',
                             'nom' => 'Test User',
                             'email' => 'test@test.com',
                             'telephone' => '0123456789',
                             'adresse' => '456 New Street',
                             'code_postal' => '69000',
                             'ville' => 'Lyon',
                             'pays' => 'FR',
                         ]);

        $response->assertRedirect(route('addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Nouveau Label',
            'adresse' => '456 New Street',
        ]);
    }

    /** @test */
    public function suppression_adresse_fonctionne()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Delete Street',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->delete(route('addresses.destroy', $address->id));

        $response->assertRedirect(route('addresses.index'));
        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    /** @test */
    public function ne_peut_pas_supprimer_adresse_par_defaut()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Default Street',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->delete(route('addresses.destroy', $address->id));

        $response->assertRedirect(route('addresses.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
        ]);
    }

    /** @test */
    public function definition_adresse_par_defaut_fonctionne()
    {
        $address1 = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
            'label' => 'Première',
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 First Street',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $address2 = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
            'label' => 'Deuxième',
            'nom' => 'Test User',
            'email' => 'test@test.com',
            'telephone' => '0123456789',
            'adresse' => '456 Second Street',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->post(route('addresses.setDefault', $address2->id));

        $response->assertRedirect(route('addresses.index'));
        
        $this->assertDatabaseHas('addresses', [
            'id' => $address2->id,
            'is_default' => true,
        ]);
        
        $this->assertDatabaseHas('addresses', [
            'id' => $address1->id,
            'is_default' => false,
        ]);
    }

    /** @test */
    public function routes_adresses_redirigent_guest_vers_login()
    {
        $response = $this->get(route('addresses.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('addresses.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ne_peut_pas_modifier_adresse_autre_utilisateur()
    {
        $otherUser = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $otherUser->id,
            'label' => 'Private',
            'nom' => 'Other User',
            'email' => 'other@test.com',
            'telephone' => '0123456789',
            'adresse' => '123 Private Street',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'FR',
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('addresses.edit', $address->id));

        $response->assertStatus(404);
    }
}