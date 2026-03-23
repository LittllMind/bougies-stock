<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bougie>
 */
class BougieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parfums = ['Vanille', 'Lavande', 'Rose', 'Santal', 'Cèdre', 'Fleur d\'oranger', 'Pin', 'Miel', 'Citronnelle', 'Patchouli'];
        $collections = ['Été', 'Hiver', 'Automne', 'Printemps', 'Premium', 'Classique', null];
        $formats = ['120g', '200g', '300g'];
        $typesCire = ['soja', 'paraffine', 'cire d\'abeille', 'coco'];

        return [
            'reference' => 'BOUG-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'parfum' => $this->faker->randomElement($parfums),
            'nom' => $this->faker->randomElement($parfums) . ' ' . $this->faker->randomElement(['Douce', 'Intense', 'Naturelle', 'Artisanale']),
            'collection' => $this->faker->randomElement($collections),
            'format' => $this->faker->randomElement($formats),
            'type_cire' => $this->faker->randomElement($typesCire),
            'temps_brulure' => $this->faker->randomElement([25, 40, 60, 80]),
            'notes' => $this->faker->optional()->sentence,
            'prix' => $this->faker->randomFloat(2, 15, 45),
            'quantite' => $this->faker->numberBetween(0, 50),
            'seuil_alerte' => $this->faker->numberBetween(3, 10),
        ];
    }

    /**
     * Indicate that the stock is low.
     */
    public function stockBas(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 2,
            'seuil_alerte' => 5,
        ]);
    }

    /**
     * Indicate that the stock is empty.
     */
    public function ruptureStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 0,
            'seuil_alerte' => 5,
        ]);
    }

    /**
     * Indicate that the stock is well supplied.
     */
    public function stockOk(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 20,
            'seuil_alerte' => 5,
        ]);
    }
}
