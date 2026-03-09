<?php

namespace Tests\Feature\Fonds;

use Tests\TestCase;
use App\Models\Fond;
use App\Models\User;
use App\Models\MouvementStock;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FondControllerActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->adminUser();
    }

    /** @test */
    public function admin_peut_incrementer_stock_via_dashboard()
    {
        $fond = Fond::factory()->create([
            'type' => 'miroir',
            'quantite' => 10
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'increment',
                'quantite' => 5
            ]);

        $response->assertRedirect(route('fonds.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 15 // 10 + 5
        ]);
    }

    /** @test */
    public function admin_peut_decrementer_stock_via_dashboard()
    {
        $fond = Fond::factory()->create([
            'type' => 'doré',
            'quantite' => 10
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'decrement',
                'quantite' => 3
            ]);

        $response->assertRedirect(route('fonds.index'));
        
        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 7 // 10 - 3
        ]);
    }

    /** @test */
    public function admin_peut_fixer_stock_directement_via_set()
    {
        $fond = Fond::factory()->create([
            'quantite' => 100
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'set',
                'quantite' => 42
            ]);

        $response->assertRedirect(route('fonds.index'));
        
        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 42
        ]);
    }

    /** @test */
    public function employe_ne_peut_pas_modifier_stock()
    {
        $fond = Fond::factory()->create(['quantite' => 10]);
        $employe = $this->employeUser();

        $response = $this->actingAs($employe)
            ->patch(route('fonds.update', $fond), [
                'action' => 'increment',
                'quantite' => 5
            ]);

        $response->assertRedirect(route('fonds.index'))
            ->assertSessionHas('error', 'Action réservée aux administrateurs');

        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 10 // Inchangé
        ]);
    }

    /** @test */
    public function impossible_de_decrementer_sous_zero()
    {
        $fond = Fond::factory()->create(['quantite' => 3]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'decrement',
                'quantite' => 10 // Plus que le stock
            ]);

        $response->assertRedirect(route('fonds.index'))
            ->assertSessionHas('error', 'Stock insuffisant');

        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'quantite' => 3 // Inchangé
        ]);
    }

    /** @test */
    public function action_increment_cree_mouvement_stock_entree()
    {
        $fond = Fond::factory()->create([
            'type' => 'miroir',
            'quantite' => 10
        ]);

        $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'increment',
                'quantite' => 5
            ]);

        $this->assertDatabaseHas('mouvements_stock', [
            'stockable_type' => Fond::class,
            'stockable_id' => $fond->id,
            'type' => 'entree',
            'quantite' => 5,
            'raison' => 'Incrémentation via dashboard admin'
        ]);
    }

    /** @test */
    public function action_decrement_cree_mouvement_stock_sortie()
    {
        $fond = Fond::factory()->create([
            'type' => 'doré',
            'quantite' => 20
        ]);

        $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'decrement',
                'quantite' => 8
            ]);

        $this->assertDatabaseHas('mouvements_stock', [
            'stockable_type' => Fond::class,
            'stockable_id' => $fond->id,
            'type' => 'sortie',
            'quantite' => 8,
            'raison' => 'Décrémentation via dashboard admin'
        ]);
    }

    /** @test */
    public function modification_stock_sans_authentification_est_refusee()
    {
        $fond = Fond::factory()->create(['quantite' => 10]);

        $response = $this->patch(route('fonds.update', $fond), [
            'action' => 'increment',
            'quantite' => 5
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('fonds', ['quantite' => 10]);
    }

    /** @test */
    public function validation_rejecte_action_invalide()
    {
        $fond = Fond::factory()->create(['quantite' => 10]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'invalid_action',
                'quantite' => 5
            ]);

        $response->assertSessionHasErrors('action');
        
        $this->assertDatabaseHas('fonds', ['quantite' => 10]);
    }

    /** @test */
    public function validation_rejecte_quantite_negative()
    {
        $fond = Fond::factory()->create(['quantite' => 10]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.update', $fond), [
                'action' => 'increment',
                'quantite' => -5
            ]);

        $response->assertSessionHasErrors('quantite');
    }

    /** @test */
    public function admin_peut_mettre_a_jour_prix_achat_et_vente()
    {
        $fond = Fond::factory()->create([
            'type' => 'miroir',
            'prix_achat' => 5,
            'prix_vente' => 10
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('fonds.updatePrix', $fond), [
                'prix_achat' => 7.50,
                'prix_vente' => 15.00
            ]);

        $response->assertRedirect(route('fonds.index'))
            ->assertSessionHas('success', 'Prix mis à jour');

        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'prix_achat' => 7.50,
            'prix_vente' => 15.00
        ]);
    }

    /** @test */
    public function employe_ne_peut_pas_modifier_prix()
    {
        $fond = Fond::factory()->create([
            'prix_achat' => 5,
            'prix_vente' => 10
        ]);
        $employe = $this->employeUser();

        $response = $this->actingAs($employe)
            ->patch(route('fonds.updatePrix', $fond), [
                'prix_achat' => 3,
                'prix_vente' => 8
            ]);

        $response->assertRedirect(route('fonds.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('fonds', [
            'id' => $fond->id,
            'prix_achat' => 5, // Inchangé
            'prix_vente' => 10
        ]);
    }
}