<?php

namespace Database\Factories;

use App\Models\Lieu;
use Illuminate\Database\Eloquent\Factories\Factory;

class LieuFactory extends Factory
{
    protected $model = Lieu::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'ville' => $this->faker->city(),
            'code_postal' => $this->faker->postcode(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'actif' => true,
        ];
    }

    /**
     * État inactif pour le lieu.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * État sans coordonnées GPS.
     */
    public function withoutCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => null,
            'longitude' => null,
        ]);
    }
}
