<?php

namespace Database\Factories;

use App\Models\Vinyle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VinyleFactory extends Factory
{
    protected $model = Vinyle::class;

    public function definition(): array
    {
        return [
            'reference' => 'VIN-' . Str::upper(fake()->unique()->bothify('??###')),
            'nom' => fake()->words(3, true),
            'artiste' => fake()->name(),
            'modele' => fake()->randomElement(['Standard', 'Miroir', 'Doré']),
            'genre' => fake()->randomElement(['Rock', 'Jazz', 'Classique', 'Pop', 'Électronique']),
            'style' => fake()->randomElement(['LP', 'EP', 'Maxi', 'Compilation']),
            'prix' => fake()->randomFloat(2, 15, 50),
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
