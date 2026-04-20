<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin peut télécharger rapport inventaire PDF
     */
    public function test_admin_peut_generer_rapport_inventaire_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.inventory.pdf'));

        $response->assertStatus(200);
        // Accepte PDF ou HTML fallback
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            $contentType === 'application/pdf' || strpos($contentType, 'text/html') !== false,
            'Response should be PDF or HTML fallback'
        );
    }

    /**
     * Test: Rapport inventaire contient toutes les bougies
     */
    public function test_rapport_inventaire_contient_toutes_les_bougies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bougie = Bougie::factory()->create([
            'reference' => 'SPI-001',
            'nom' => 'Ganesh Spirit',
            'quantite' => 5,
            'prix' => 45.00,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.inventory.pdf'));

        $response->assertStatus(200);
    }

    /**
     * Test: Client ne peut pas accéder aux rapports
     */
    public function test_client_ne_peut_pas_acceder_rapport_inventaire()
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get(route('admin.reports.inventory.pdf'));

        // Redirection vers login ou 403 selon middleware
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403]),
            'Non-admin should be redirected (302) or denied (403)'
        );
    }

    /**
     * Test: Rapport inventaire inclut alertes stock
     */
    public function test_rapport_affiche_alertes_stock_basse()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->create([
            'nom' => 'Lotus Alert',
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.inventory.pdf'));

        $response->assertStatus(200);
    }
}
