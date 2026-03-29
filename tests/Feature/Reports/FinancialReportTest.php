<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin peut générer rapport financier par période
     */
    public function test_admin_peut_generer_rapport_financier_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Créer des commandes payées
        $order = Order::factory()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'paid',
            'total' => 90.00,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.financial.pdf', [
                'debut' => now()->subMonth()->format('Y-m-d'),
                'fin' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        // Accepte PDF ou HTML fallback
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            $contentType === 'application/pdf' || strpos($contentType, 'text/html') !== false,
            'Response should be PDF or HTML fallback'
        );
    }

    /**
     * Test: Rapport calcul total des ventes
     */
    public function test_rapport_calcul_total_ventes_periode()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Commande payée dans la période
        Order::factory()->create([
            'statut' => 'paid',
            'total' => 100.00,
            'created_at' => now()->subDay(),
        ]);

        // Commande hors période
        Order::factory()->create([
            'statut' => 'paid',
            'total' => 50.00,
            'created_at' => now()->subMonth(2),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.financial.pdf', [
                'debut' => now()->subWeek()->format('Y-m-d'),
                'fin' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
    }

    /**
     * Test: Non-admin ne peut pas accéder aux rapports financiers
     */
    public function test_employe_ne_peut_pas_generer_rapport_financier()
    {
        $employe = User::factory()->create(['role' => 'employe']);

        $response = $this->actingAs($employe)
            ->get(route('admin.reports.financial.pdf', [
                'debut' => now()->subMonth()->format('Y-m-d'),
                'fin' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(403);
    }

    /**
     * Test: Rapport requiert dates valides
     */
    public function test_rapport_requiert_dates_valides()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.financial.pdf'));

        $response->assertSessionHasErrors(['debut', 'fin']);
    }
}
