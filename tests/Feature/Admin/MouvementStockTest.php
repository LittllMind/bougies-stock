<?php

namespace Tests\Feature\Admin;

use App\Models\Bougie;
use App\Models\MouvementStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MouvementStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employe;
    protected Bougie $bougie;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employe = User::factory()->create(['role' => 'employe']);
        $this->bougie = Bougie::factory()->create([
            'nom' => 'Bougie Test',
            'quantite' => 10,
        ]);
    }

    // ========== ACCÈS ROUTES ==========

    /** @test */
    public function admin_peut_voir_liste_mouvements()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.mouvements.index');
    }

    /** @test */
    public function employe_peut_voir_liste_mouvements()
    {
        $response = $this->actingAs($this->employe)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function client_ne_peut_pas_voir_liste_mouvements()
    {
        $client = User::factory()->create(['role' => 'client']);
        
        $response = $this->actingAs($client)
            ->get(route('admin.mouvements.index'));

        // Le middleware redirige vers le catalogue au lieu de 403
        $response->assertStatus(302)->assertRedirect(route('catalogue'));
    }

    /** @test */
    public function invite_ne_peut_pas_voir_liste_mouvements()
    {
        $response = $this->get(route('admin.mouvements.index'));

        $response->assertRedirect(route('login'));
    }

    // ========== AFFICHAGE DONNÉES ==========

    /** @test */
    public function liste_affiche_mouvements_avec_pagination()
    {
        MouvementStock::factory()->count(30)->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200)
            ->assertViewHas('mouvements')
            ->assertSee('Historique des Mouvements');
    }

    /** @test */
    public function vue_affiche_statistiques_mouvements()
    {
        MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'quantite' => 10,
        ]);

        MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'quantite' => 5,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200)
            ->assertViewHas('stats');
    }

    // ========== FILTRES ==========

    /** @test */
    public function filtre_par_type_entree()
    {
        $entree = MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $sortie = MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', ['type' => 'entree']));

        $response->assertStatus(200)
            ->assertSee($entree->reference)
            ->assertDontSee($sortie->reference);
    }

    /** @test */
    public function filtre_par_type_sortie()
    {
        $entree = MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $sortie = MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', ['type' => 'sortie']));

        $response->assertStatus(200)
            ->assertSee($sortie->reference)
            ->assertDontSee($entree->reference);
    }

    /** @test */
    public function filtre_par_bougie()
    {
        $bougie2 = Bougie::factory()->create(['nom' => 'Autre Bougie']);

        $mouvement1 = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $mouvement2 = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $bougie2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', ['bougie_id' => $this->bougie->id]));

        $response->assertStatus(200)
            ->assertSee($mouvement1->reference)
            ->assertDontSee($mouvement2->reference);
    }

    /** @test */
    public function filtre_par_date_debut()
    {
        $ancien = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(10),
        ]);

        $recent = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', [
                'date_debut' => now()->subDays(5)->format('Y-m-d')
            ]));

        $response->assertStatus(200)
            ->assertSee($recent->reference)
            ->assertDontSee($ancien->reference);
    }

    /** @test */
    public function filtre_par_date_fin()
    {
        $ancien = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(10),
        ]);

        $recent = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', [
                'date_fin' => now()->subDays(5)->format('Y-m-d')
            ]));

        $response->assertStatus(200)
            ->assertSee($ancien->reference)
            ->assertDontSee($recent->reference);
    }

    /** @test */
    public function filtre_par_periode_combine()
    {
        $tropAncien = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(20),
        ]);

        $dansPeriode = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(5),
        ]);

        $tropRecent = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', [
                'date_debut' => now()->subDays(10)->format('Y-m-d'),
                'date_fin' => now()->subDays(2)->format('Y-m-d'),
            ]));

        $response->assertStatus(200)
            ->assertSee($dansPeriode->reference)
            ->assertDontSee($tropAncien->reference)
            ->assertDontSee($tropRecent->reference);
    }

    // ========== COMBINAISON FILTRES ==========

    /** @test */
    public function filtres_combines_fonctionnent()
    {
        $mouvementMatch = MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now(),
        ]);

        $mouvementNonMatch = MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(10),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', [
                'type' => 'entree',
                'bougie_id' => $this->bougie->id,
                'date_debut' => now()->subDays(5)->format('Y-m-d'),
            ]));

        $response->assertStatus(200)
            ->assertSee($mouvementMatch->reference)
            ->assertDontSee($mouvementNonMatch->reference);
    }

    // ========== PAGINATION ==========

    /** @test */
    public function pagination_fonctionne()
    {
        MouvementStock::factory()->count(30)->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200)
            ->assertViewHas('mouvements', function ($mouvements) {
                return $mouvements->count() <= 25;
            });
    }

    /** @test */
    public function deuxieme_page_fonctionne()
    {
        MouvementStock::factory()->count(30)->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index', ['page' => 2]));

        $response->assertStatus(200)
            ->assertViewHas('mouvements');
    }

    // ========== VUE VIDE ==========

    /** @test */
    public function vue_affiche_message_si_aucun_mouvement()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mouvements.index'));

        $response->assertStatus(200)
            ->assertSee('Aucun mouvement trouvé');
    }

    // ========== SCOPES MODÈLE ==========

    /** @test */
    public function scope_entrees_retourne_entrees()
    {
        $entree = MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $sortie = MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $entrees = MouvementStock::entrees()->get();

        $this->assertTrue($entrees->contains($entree));
        $this->assertFalse($entrees->contains($sortie));
    }

    /** @test */
    public function scope_sorties_retourne_sorties()
    {
        $entree = MouvementStock::factory()->create([
            'type' => 'entree',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $sortie = MouvementStock::factory()->create([
            'type' => 'sortie',
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $sorties = MouvementStock::sorties()->get();

        $this->assertTrue($sorties->contains($sortie));
        $this->assertFalse($sorties->contains($entree));
    }

    /** @test */
    public function scope_par_produit_filtre_correctement()
    {
        $bougie2 = Bougie::factory()->create();

        $mouvement1 = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
        ]);

        $mouvement2 = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $bougie2->id,
        ]);

        $result = MouvementStock::parProduit('bougie', $this->bougie->id)->get();

        $this->assertTrue($result->contains($mouvement1));
        $this->assertFalse($result->contains($mouvement2));
    }

    /** @test */
    public function scope_par_periode_filtre_correctement()
    {
        $dansPeriode = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(5),
        ]);

        $horsPeriode = MouvementStock::factory()->create([
            'produit_type' => 'bougie',
            'produit_id' => $this->bougie->id,
            'date_mouvement' => now()->subDays(20),
        ]);

        $result = MouvementStock::parPeriode(
            now()->subDays(10),
            now()
        )->get();

        $this->assertTrue($result->contains($dansPeriode));
        $this->assertFalse($result->contains($horsPeriode));
    }
}