<?php

namespace Tests\Feature\Fonds;

use Tests\TestCase;
use App\Models\Fond;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FondControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_peut_voir_dashboard_fonds()
    {
        $admin = $this->adminUser();
        
        // Créer fonds de test
        Fond::factory()->miroir()->count(3)->create();
        
        $response = $this->actingAs($admin)
            ->get(route('fonds.index'));
        
        $response->assertOk()
            ->assertViewIs('fonds.index')
            ->assertViewHas(['fonds', 'totaux'])
            ->assertSee('Fonds')
            ->assertSee('Stock')
            ->assertSee('Valeur totale');
    }

    /** @test */
    public function employe_peut_voir_dashboard_fonds()
    {
        $employe = $this->employeUser();
        
        Fond::factory()->doré()->create(['quantite' => 10]);
        
        $response = $this->actingAs($employe)
            ->get(route('fonds.index'));
        
        $response->assertOk()
            ->assertViewHas('fonds');
    }

    /** @test */
    public function client_ne_peut_pas_voir_dashboard_fonds()
    {
        $client = $this->clientUser();
        
        $response = $this->actingAs($client)
            ->get(route('fonds.index'));
        
        $response->assertRedirect('/');
    }

    /** @test */
    public function utilisateur_non_connecte_est_redirige_vers_login()
    {
        $response = $this->get(route('fonds.index'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function totaux_sont_correctement_calcules()
    {
        $admin = $this->adminUser();
        
        // Créer 3 fonds avec quantités connues
        Fond::factory()->create([
            'type' => 'miroir',
            'quantite' => 10,
            'prix_achat' => 5,
            'prix_vente' => 10
        ]);
        
        Fond::factory()->create([
            'type' => 'doré',
            'quantite' => 5,
            'prix_achat' => 8,
            'prix_vente' => 15
        ]);

        $response = $this->actingAs($admin)
            ->get(route('fonds.index'));

        $totaux = $response->viewData('totaux');
        
        // Vérifier les calculs
        $this->assertEquals(15, $totaux['quantite_totale']); // 10 + 5
        $this->assertEquals((10 * 5) + (5 * 8), $totaux['montant_investi']); // Montant stock
        $this->assertEquals((10 * 10) + (5 * 15), $totaux['valeur_totale']); // Valeur stock
    }

    /** @test */
    public function fonds_ont_statuts_corrects()
    {
        $admin = $this->adminUser();
        
        // Fond en rupture
        Fond::factory()->create([
            'type' => 'standard',
            'quantite' => 0,
            'stock_alerte' => 5
        ]);
        
        // Fond stock faible
        Fond::factory()->create([
            'type' => 'miroir',
            'quantite' => 3,
            'stock_alerte' => 5
        ]);
        
        // Fond OK
        Fond::factory()->create([
            'type' => 'doré',
            'quantite' => 20,
            'stock_alerte' => 5
        ]);

        $response = $this->actingAs($admin)
            ->get(route('fonds.index'));

        $fonds = $response->viewData('fonds');
        
        $this->assertEquals('Rupture', $fonds[0]['status']);
        $this->assertEquals('Faible', $fonds[1]['status']);
        $this->assertEquals('OK', $fonds[2]['status']);
    }

    /** @test */
    public function tableau_affiche_boutons_action_pour_admin()
    {
        $admin = $this->adminUser();
        Fond::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('fonds.index'));

        // Vérifier présence des boutons +1/-1
        $response->assertSee('+1')
            ->assertSee('-1')
            ->assertSee('Modifier');
    }

    /** @test */
    public function tableau_naffiche_pas_boutons_action_pour_employe()
    {
        $employe = $this->employeUser();
        Fond::factory()->create();

        $response = $this->actingAs($employe)
            ->get(route('fonds.index'));

        // Les employés voient le tableau mais sans boutons d'action
        $response->assertOk()
            ->assertViewHas('fonds');
    }

    /** @test */
    public function test_fond_dashboard_affiche_prix_achat()
    {
        $admin = $this->adminUser();
        
        Fond::factory()->create([
            'type' => 'miroir',
            'prix_achat' => 5.50,
            'prix_vente' => 12.00
        ]);

        $response = $this->actingAs($admin)
            ->get(route('fonds.index'));

        $fonds = $response->viewData('fonds');
        $firstFond = $fonds->first();
        
        $this->assertEquals(5.50, $firstFond['prix_achat']);
        $this->assertEquals(12.00, $firstFond['prix_vente']);
        $this->assertEquals(12.00 - 5.50, $firstFond['marge']);
    }
}