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

    public function test_anonymous_cart_merges_on_login()
    {
        // Create a vinyle with stock
        $vin = Vinyle::create([
            'nom' => 'Test Vinyle',
            'modele' => 'Test',
            'prix' => 12.5,
            'quantite' => 10,
        ]);

        // Start a guest session and add one item to the cart
        $this->get(route('kiosque.index'));

        $this->post(route('cart.add'), [
            'vinyle_id' => $vin->id,
            'quantite' => 1,
        ]);

        // Ensure an anonymous cart exists
        $anonCart = Cart::whereNull('user_id')->first();
        $this->assertNotNull($anonCart, 'Anonymous cart should exist after adding as guest');
        $this->assertEquals(1, $anonCart->items()->count());

        // Create a user and login (using real login route so middleware flow runs)
        $user = User::create([
            'name' => 'Merge Tester',
            'email' => 'merge_test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Trigger a subsequent request so MergeCartOnLogin middleware runs
        $this->get(route('cart.index'));

        // The anonymous cart should have been removed and a user cart created/filled
        $this->assertDatabaseMissing('carts', ['id' => $anonCart->id, 'user_id' => null]);

        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart, 'User cart should exist after merge');
        $this->assertGreaterThanOrEqual(1, $userCart->items()->count(), 'User cart should contain at least the merged item');
    }

    public function test_merge_combines_with_existing_user_cart()
    {
        // Create items and user
        $vin = Vinyle::create([
            'nom' => 'Test Vinyle 2',
            'modele' => 'Test',
            'prix' => 15.0,
            'quantite' => 10,
        ]);

        $user = User::create([
            'name' => 'Merge Tester 2',
            'email' => 'merge2@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create existing user cart with one item
        $this->actingAs($user);
        $this->post(route('cart.add'), [
            'vinyle_id' => $vin->id,
            'quantite' => 1,
        ]);
        $this->assertEquals(1, Cart::where('user_id', $user->id)->first()->items()->count());
        // Logout
        auth()->logout();

        // As guest, add the same vinyle
        $this->get(route('kiosque.index'));
        $this->post(route('cart.add'), [
            'vinyle_id' => $vin->id,
            'quantite' => 2,
        ]);

        $anonCart = Cart::whereNull('user_id')->first();
        $this->assertNotNull($anonCart);

        // Login via real route
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Trigger merge via a request
        $this->get(route('cart.index'));

        // Assert user cart now has combined quantity (capped by stock if necessary)
        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);

        $totalQty = $userCart->items()->first()->quantite;
        $this->assertEquals(3, $totalQty, 'Quantities should be combined into the user cart (1 + 2 = 3)');
    }
}
