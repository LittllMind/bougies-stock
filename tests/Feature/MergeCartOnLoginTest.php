<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Vinyle;
use App\Models\Cart;

class MergeCartOnLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_items_are_attached_to_user_after_login()
    {
        // 1. Créer un vinyle avec du stock
        $vin = Vinyle::create([
            'nom'      => 'Test Vinyle',
            'modele'   => 'Test',
            'prix'     => 12.5,
            'quantite' => 10,
        ]);

        // 2. En tant qu'invité, aller sur le kiosque puis ajouter un article au panier
        $this->get(route('kiosque.index'));

        $this->post(route('cart.add'), [
            'vinyle_id' => $vin->id,
            'quantite'  => 1,
        ]);

        // 3. Vérifier qu’un panier anonyme existe bien avec au moins un item
        $anonCart = Cart::whereNull('user_id')->first();
        $this->assertNotNull($anonCart, 'Un panier invité doit exister après ajout au panier');
        $guestItemCount = $anonCart->items()->count();
        $this->assertGreaterThan(0, $guestItemCount, 'Le panier invité doit contenir au moins un article');

        // 4. Créer un user et le connecter via la vraie route de login
        $user = User::create([
            'name'     => 'Merge Tester',
            'email'    => 'merge_test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect(); // ou ->assertStatus(302) selon ton flow

        // 5. Déclencher le middleware de merge en visitant la page panier
        $this->get(route('cart.index'));

        // 6. Vérifier qu’un panier rattaché à l’utilisateur existe
        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart, 'Un panier utilisateur doit exister après connexion');

        // 7. Vérifier que le user a au moins autant d’items que le panier invité initial
        $this->assertGreaterThanOrEqual(
            $guestItemCount,
            $userCart->items()->count(),
            'Les articles du panier invité doivent être présents dans le panier utilisateur après connexion'
        );

        // Optionnel : vérifier que le vinyle précis est bien présent
        $this->assertTrue(
            $userCart->items()->where('vinyle_id', $vin->id)->exists(),
            'Le vinyle ajouté en tant qu’invité doit être présent dans le panier utilisateur'
        );
    }
}
