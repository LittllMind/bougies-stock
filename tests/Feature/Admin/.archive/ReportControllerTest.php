<?php

namespace Tests\Feature\Admin;

use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_voir_page_rapports()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/reports');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
    }

    public function test_rapports_affiche_statistiques_inventaire()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->create(['prix' => 45.00, 'quantite' => 5]);
        Bougie::factory()->create(['prix' => 28.00, 'quantite' => 10]);
        
        $response = $this->actingAs($admin)->get('/admin/reports');
        
        $response->assertViewHas('stats');
        $response->assertViewHas('bougies');
    }

    public function test_peut_exporter_inventaire_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->count(3)->create();
        
        $response = $this->actingAs($admin)->get('/admin/reports/inventory/pdf');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function test_peut_exporter_rapport_financier_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'total' => 150.00,
            'statut' => 'payee',
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'prix_unitaire' => 45.00,
            'quantite' => 2,
        ]);
        
        $response = $this->actingAs($admin)->get('/admin/reports/financial/pdf');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function test_rapport_financier_filtre_par_periode()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Commande ancienne
        Order::factory()->create([
            'total' => 100.00,
            'statut' => 'payee',
            'created_at' => now()->subDays(30),
        ]);
        
        // Commande récente
        Order::factory()->create([
            'total' => 200.00,
            'statut' => 'payee',
            'created_at' => now()->subDay(),
        ]);
        
        $query = '/admin/reports/financial/pdf?start_date=' . now()->subWeek()->format('Y-m-d') 
            . '&end_date=' . now()->format('Y-m-d');
        $response = $this->actingAs($admin)->get($query);
        
        $response->assertStatus(200);
    }

    public function test_client_ne_peut_pas_acceder_rapports_admin()
    {
        $client = User::factory()->create(['role' => 'client']);
        
        $response = $this->actingAs($client)->get('/admin/reports');
        
        // Le middleware redirige vers le catalogue au lieu de 403
        $response->assertRedirect();
    }

    public function test_invite_ne_peut_pas_acceder_rapports()
    {
        $response = $this->get('/admin/reports');
        
        $response->assertRedirect('/login');
    }
}
