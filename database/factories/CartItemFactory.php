<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Bougie;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'bougie_id' => Bougie::factory(),
            'quantite' => $this->faker->numberBetween(1, 5),
            'prix_unitaire' => $this->faker->randomFloat(2, 10, 100),
        ];
    }

    public function forCart(Cart $cart): static
    {
        return $this->state(fn (array $attributes) => [
            'cart_id' => $cart->id,
        ]);
    }

    public function forBougie(Bougie $bougie): static
    {
        return $this->state(fn (array $attributes) => [
            'bougie_id' => $bougie->id,
            'prix_unitaire' => $bougie->prix,
        ]);
    }
}
