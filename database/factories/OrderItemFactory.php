<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantite = fake()->numberBetween(1, 5);
        $prixUnitaire = fake()->randomFloat(2, 15, 50);
        
        return [
            'order_id' => Order::factory(),
            'bougie_id' => null, // Sera défini par le test si nécessaire
            'vinyle_id' => null, // Legacy - plus utilisé
            'fond_id' => null, // Legacy - plus utilisé
            'titre_vinyle' => null, // Legacy
            'artiste_vinyle' => null, // Legacy
            'reference_vinyle' => null, // Legacy
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'total' => $quantite * $prixUnitaire,
        ];
    }

    /**
     * État : Avec bougie
     */
    public function withBougie(): static
    {
        return $this->state(fn () => [
            'bougie_id' => \App\Models\Bougie::factory(),
        ]);
    }

    /**
     * État : Sans bougie
     */
    public function withoutBougie(): static
    {
        return $this->state(fn () => [
            'bougie_id' => null,
        ]);
    }
}
