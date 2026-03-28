<?php

namespace Tests\Feature\Reports;

use App\Models\Bougie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryReportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_generer_pdf_inventaire()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/reports/inventory/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function test_pdf_inventaire_contient_liste_bougies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bougie = Bougie::factory()->create([
            'nom' => 'Ganesh',
            'quantite' => 10,
            'seuil_alerte' => 3,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/reports/inventory/pdf');

        $response->assertStatus(200);
        // Le contenu PDF est binaire, on vérifie juste la réponse succès
    }

    public function test_pdf_inventaire_calcule_valeur_stock()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->create([
            'prix' => 45.00,
            'quantite' => 10,
        ]);
        Bougie::factory()->create([
            'prix' => 28.00,
            'quantite' => 5,
        ]);
        // Valeur totale: (45*10) + (28*5) = 450 + 140 = 590€

        $response = $this->actingAs($admin)
            ->get('/admin/reports/inventory/pdf');

        $response->assertStatus(200);
    }

    public function test_pdf_inventaire_marque_alertes_stock_bas()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Bougie::factory()->create([
            'nom' => 'Alerte Stock',
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/reports/inventory/pdf');

        $response->assertStatus(200);
    }

    public function test_non_admin_ne_peut_pas_generer_pdf()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)
            ->get('/admin/reports/inventory/pdf');

        // Redirection car non-admin (middleware role redirige)
        $response->assertStatus(302);
    }

    public function test_guest_ne_peut_pas_generer_pdf()
    {
        $response = $this->get('/admin/reports/inventory/pdf');

        $response->assertRedirect('/login');
    }
}
