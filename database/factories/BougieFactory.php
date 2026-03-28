<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $typesForme = ['sculpture', 'chandelle', 'votive', 'pilier'];
        $collections = ['Spirit', 'Art', 'Nature'];
        $formats = ['sculpture', 'chandelle', 'votive', '200g', '300g'];

        $format = $this->faker->randomElement($formats);
        $collection = $this->faker->randomElement($collections);

        // Génération réaliste du nom selon la forme
        $nom = match($format) {
            'sculpture' => $this->faker->randomElement(['Ganesh', 'Lotus', 'Chat', 'Ruche', 'Nest', 'Étoile']),
            'chandelle' => 'La Chandelle',
            'votive' => 'Votive ' . $this->faker->randomElement(['Douceur', 'Sérénité', 'Lumière']),
            default => 'Bougie ' . $this->faker->word(),
        };

        $parfums = ["Parfum naturel de cire d'abeille - Vanille", "Parfum naturel de cire d'abeille - Lavande", "Parfum naturel de cire d'abeille - Rose", "Parfum naturel de cire d'abeille - Santal"];

        return [
            'reference' => 'BOUG-' . strtoupper(substr(uniqid(), -6)),
            'parfum' => $this->faker->randomElement($parfums),
            'nom' => $nom,
            'collection' => $collection,
            'format' => $format,
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => $this->faker->randomElement([20, 35, 45, 50, 60]),
            'notes' => "Bougie artisanale coulée à la main en cire d'abeille pure.",
            'prix' => $this->faker->randomFloat(2, 16, 45),
            'quantite' => $this->faker->numberBetween(0, 25),
            'seuil_alerte' => 3,
        ];
    }

    public function stockOk(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => $this->faker->numberBetween(8, 25),
        ]);
    }

    public function stockBas(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 2,
        ]);
    }

    public function ruptureStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantite' => 0,
        ]);
    }

    public function sculpture(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'sculpture',
            'collection' => 'Art',
            'prix' => $this->faker->randomFloat(2, 28, 45),
        ]);
    }

    public function chandelle(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'chandelle',
            'collection' => 'Nature',
            'nom' => 'La Chandelle',
            'prix' => $this->faker->randomFloat(2, 18, 25),
        ]);
    }
}
