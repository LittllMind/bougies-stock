<?php

namespace Database\Factories;

use App\Models\Bougie;
use App\Models\StockAlert;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockAlertFactory extends Factory
{
    protected $model = StockAlert::class;

    public function definition(): array
    {
        return [
            'stockable_type' => Bougie::class,
            'stockable_id' => Bougie::factory(),
            'quantite_actuelle' => $this->faker->numberBetween(0, 5),
            'seuil_alerte' => $this->faker->numberBetween(3, 10),
            'statut' => $this->faker->randomElement(['actif', 'resolu']),
            'derniere_notification_envoyee' => null,
            'resolue' => false,
            'resolved_at' => null,
        ];
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'actif',
            'resolue' => false,
            'resolved_at' => null,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'resolu',
            'resolue' => true,
            'resolved_at' => now(),
        ]);
    }

    public function rupture(): self
    {
        return $this->state(fn (array $attributes) => [
            'quantite_actuelle' => 0,
            'statut' => 'actif',
        ]);
    }

    public function faible(): self
    {
        return $this->state(fn (array $attributes) => [
            'quantite_actuelle' => $this->faker->numberBetween(1, 3),
            'seuil_alerte' => 5,
            'statut' => 'actif',
        ]);
    }
}