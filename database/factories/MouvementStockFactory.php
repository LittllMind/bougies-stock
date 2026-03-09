<?php

namespace Database\Factories;

use App\Models\MouvementStock;
use App\Models\Vinyle;
use App\Models\Fond;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MouvementStockFactory extends Factory
{
    protected $model = MouvementStock::class;

    public function definition(): array
    {
        return [
            'mouvementable_type' => Vinyle::class,
            'mouvementable_id' => Vinyle::factory(),
            'type' => fake()->randomElement(['entree', 'sortie']),
            'quantite' => fake()->numberBetween(1, 10),
            'stock_avant' => fake()->numberBetween(5, 50),
            'stock_apres' => fake()->numberBetween(5, 50),
            'motif' => fake()->sentence(),
            'user_id' => User::factory(),
            'order_id' => null,
        ];
    }

    /**
     * État : Entrée
     */
    public function entree(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'entree',
            'stock_apres' => $attributes['stock_avant'] + $attributes['quantite'],
        ]);
    }

    /**
     * État : Sortie
     */
    public function sortie(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sortie',
            'stock_apres' => max(0, $attributes['stock_avant'] - $attributes['quantite']),
        ]);
    }

    /**
     * État : Sur commande
     */
    public function pourCommande(int $orderId): static
    {
        return $this->state(fn () => [
            'order_id' => $orderId,
            'motif' => 'Vente',
        ]);
    }

    /**
     * État : Sur fond
     */
    public function pourFond(): static
    {
        return $this->state(fn () => [
            'mouvementable_type' => Fond::class,
            'mouvementable_id' => Fond::factory(),
        ]);
    }
}
