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
            'nom_bougie' => null, // Snapshot du nom si bougie_id manquant
            'parfum' => null, // Snapshot du parfum si bougie_id manquant
            'reference_bougie' => null, // Snapshot de la référence si bougie_id manquant
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
