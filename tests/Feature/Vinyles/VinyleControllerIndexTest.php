<?php

namespace Tests\Feature\Vinyles;

use App\Models\User;
use App\Models\Vinyle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VinyleControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_vinyles_list(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('vinyles.index'));

        $response->assertOk()
            ->assertViewIs('vinyles.index')
            ->assertViewHas('vinyles');
    }

    public function test_employe_can_view_vinyles_list(): void
    {
        $employe = $this->employeUser();
        Vinyle::factory()->count(3)->create();

        $response = $this->actingAs($employe)->get(route('vinyles.index'));

        $response->assertOk()
            ->assertViewIs('vinyles.index')
            ->assertViewHas('vinyles');
    }

    public function test_client_is_redirected_from_vinyles(): void
    {
        $client = $this->clientUser();

        $response = $this->actingAs($client)->get(route('vinyles.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('vinyles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_search_by_title(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->create(['nom' => 'Dark Side of the Moon']);
        Vinyle::factory()->create(['nom' => 'Abbey Road']);

        $response = $this->actingAs($admin)->get(route('vinyles.index', ['search' => 'Dark']));

        $response->assertOk()
            ->assertSee('Dark Side of the Moon')
            ->assertDontSee('Abbey Road');
    }

    public function test_search_by_artist(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->create(['artiste' => 'Pink Floyd', 'nom' => 'Album 1']);
        Vinyle::factory()->create(['artiste' => 'The Beatles', 'nom' => 'Album 2']);

        $response = $this->actingAs($admin)->get(route('vinyles.index', ['search' => 'Pink']));

        $response->assertOk()
            ->assertSee('Pink Floyd')
            ->assertDontSee('The Beatles');
    }

    public function test_search_by_reference(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->create(['reference' => 'VIN-001', 'nom' => 'Album A']);
        Vinyle::factory()->create(['reference' => 'VIN-002', 'nom' => 'Album B']);

        $response = $this->actingAs($admin)->get(route('vinyles.index', ['search' => 'VIN-001']));

        $response->assertOk()
            ->assertSee('VIN-001')
            ->assertDontSee('VIN-002');
    }

    public function test_filter_low_stock(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->create(['nom' => 'Low Stock', 'quantite' => 2]);
        Vinyle::factory()->create(['nom' => 'Normal Stock', 'quantite' => 10]);
        Vinyle::factory()->create(['nom' => 'Out of Stock', 'quantite' => 0]);

        $response = $this->actingAs($admin)->get(route('vinyles.index', ['filter' => 'stock_bas']));

        $response->assertOk()
            ->assertSee('Low Stock')
            ->assertDontSee('Normal Stock')
            ->assertDontSee('Out of Stock');
    }

    public function test_filter_out_of_stock(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->create(['nom' => 'Low Stock', 'quantite' => 2]);
        Vinyle::factory()->create(['nom' => 'Normal Stock', 'quantite' => 10]);
        Vinyle::factory()->create(['nom' => 'Out of Stock', 'quantite' => 0]);

        $response = $this->actingAs($admin)->get(route('vinyles.index', ['filter' => 'rupture']));

        $response->assertOk()
            ->assertSee('Out of Stock')
            ->assertDontSee('Normal Stock')
            ->assertDontSee('Low Stock');
    }

    public function test_pagination_works(): void
    {
        $admin = $this->adminUser();
        Vinyle::factory()->count(30)->create();

        $response = $this->actingAs($admin)->get(route('vinyles.index'));

        $response->assertOk()
            ->assertViewHas('vinyles', function ($vinyles) {
                return $vinyles->count() === 25;
            });
    }
}
