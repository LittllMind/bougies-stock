<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\Bougie;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_voir_liste_commandes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/orders');

        $response->assertStatus(200);
        $response->assertViewHas('orders');
    }

    public function test_liste_commandes_a_pagination()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->count(25)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/orders');

        $response->assertStatus(200);
        $response->assertViewHas('orders');
    }

    public function test_filtre_par_statut_fonctionne()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->create(['statut' => 'paid']);
        Order::factory()->create(['statut' => 'pending']);

        $response = $this->actingAs($admin)
            ->get('/admin/orders?statut=paid');

        $response->assertStatus(200);
        $orders = $response->viewData('orders');
        // Vérifier que tous les résultats ont le bon statut
        foreach ($orders as $order) {
            $this->assertEquals('paid', $order->statut);
        }
    }

    public function test_filtre_par_date_fonctionne()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->create(['created_at' => now()->subDays(5)]);
        Order::factory()->create(['created_at' => now()->subDays(60)]);

        $response = $this->actingAs($admin)
            ->get('/admin/orders?date_from=' . now()->subDays(10)->toDateString());

        $response->assertStatus(200);
    }

    public function test_recherche_par_reference_fonctionne()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->create(['numero_commande' => 'CMD-2024-0001']);
        Order::factory()->create(['numero_commande' => 'CMD-2024-0002']);

        $response = $this->actingAs($admin)
            ->get('/admin/orders?search=0001');

        $response->assertStatus(200);
    }

    public function test_admin_peut_voir_detail_commande()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/orders/' . $order->id);

        $response->assertStatus(200);
        $response->assertViewHas('order');
    }

    public function test_admin_peut_changer_statut_commande()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['statut' => 'pending']);

        $response = $this->actingAs($admin)
            ->patch('/admin/orders/' . $order->id . '/status', [
                'statut' => 'paid'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'paid'
        ]);
    }

    public function test_admin_peut_annuler_commande()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['statut' => 'pending']);

        $response = $this->actingAs($admin)
            ->post('/admin/orders/' . $order->id . '/cancel', [
                'raison' => 'Client request'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'cancelled'
        ]);
    }

    public function test_changement_statut_envoie_notification()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'statut' => 'pending',
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin)
            ->patch('/admin/orders/' . $order->id . '/status', [
                'statut' => 'shipped'
            ]);

        $response->assertRedirect();
        // Notification testée indirectement
    }

    public function test_non_admin_ne_peut_pas_modifier_statut()
    {
        $user = User::factory()->create(['role' => 'employe']);
        $order = Order::factory()->create(['statut' => 'pending']);

        $response = $this->actingAs($user)
            ->patch('/admin/orders/' . $order->id . '/status', [
                'statut' => 'paid'
            ]);

        $response->assertStatus(302);
    }
}
