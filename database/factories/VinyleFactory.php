<?php

namespace Database\Factories;

use App\Models\Vinyle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VinyleFactory extends Factory
{
    protected $model = Vinyle::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->words(3, true), // "Lorem Ipsum Dolor"
            'modele' => fake()->randomElement(['Standard', 'Miroir', 'Doré']),
            'prix' => fake()->randomFloat(2, 15, 50), // Entre 15.00 et 50.00
            'quantite' => fake()->numberBetween(0, 20),
            'seuil_alerte' => 3,
        ];
    }

    /**
     * État : Stock critique
     */
    public function critique(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 0,
        ]);
    }

    /**
     * État : Stock bas
     */
    public function stockBas(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 2,
            'seuil_alerte' => 3,
        ]);
    }
}
