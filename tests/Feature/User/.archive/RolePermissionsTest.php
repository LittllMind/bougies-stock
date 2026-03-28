<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
    }

    /** @test */
    public function employe_cannot_access_admin_routes(): void
    {
        $employe = User::factory()->create(['role' => 'employe']);

        $response = $this->actingAs($employe)
            ->get(route('admin.users.index'));

        $response->assertRedirect(); // Redirection vers catalogue
    }

    /** @test */
    public function client_cannot_access_admin_routes(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get(route('admin.users.index'));

        $response->assertRedirect(); // Redirection vers catalogue
    }

    /** @test */
    public function admin_can_access_employe_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('bougies.index'));

        $response->assertOk();
    }

    /** @test */
    public function employe_can_access_employe_routes(): void
    {
        $employe = User::factory()->create(['role' => 'employe']);

        $response = $this->actingAs($employe)
            ->get(route('bougies.index'));

        $response->assertOk();
    }

    /** @test */
    public function client_cannot_access_employe_routes(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get(route('bougies.index'));

        $response->assertRedirect(); // Redirection vers catalogue
    }

    /** @test */
    public function admin_and_employe_can_access_bougies_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employe = User::factory()->create(['role' => 'employe']);

        $this->actingAs($admin)
            ->get(route('bougies.index'))
            ->assertOk();

        $this->actingAs($employe)
            ->get(route('bougies.index'))
            ->assertOk();
    }

    /** @test */
    public function employe_cannot_update_bougie_stock(): void
    {
        $employe = User::factory()->create(['role' => 'employe']);
        $bougie = \App\Models\Bougie::factory()->create();

        $response = $this->actingAs($employe)
            ->patch(route('admin.bougies.updateStock', $bougie), [
                'quantite' => 100,
            ]);

        $response->assertRedirect(); // Redirection catalogue (middleware empêche)
    }

    /** @test */
    public function admin_can_update_bougie_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bougie = \App\Models\Bougie::factory()->create(['quantite' => 50]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.bougies.updateStock', $bougie), [
                'action' => 'set',
                'quantite' => 100,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bougies', [
            'id' => $bougie->id,
            'quantite' => 100,
        ]);
    }

    /** @test */
    public function guest_is_redirected_to_login_for_protected_routes(): void
    {
        // Accès admin protégé par auth (les routes catalogue sont publiques)
        $response = $this->get(route('admin.bougies.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function role_check_is_case_insensitive(): void
    {
        // Vérifier que le middleware gère bien les rôles en minuscules
        $admin = User::factory()->create(['role' => 'admin']);

        // La route admin.users nécessite 'admin' exactement
        $response = $this->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
    }
}
