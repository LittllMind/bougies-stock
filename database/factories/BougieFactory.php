<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
<<<<<<< HEAD
use Illuminate\Support\Str;
=======
>>>>>>> origin/master

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
<<<<<<< HEAD
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
=======
        $parfums = ['Vanille', 'Lavande', 'Rose', 'Cèdre', 'Coco', 'Menthe', 'Ylang-Ylang', 'Santal', 'Figue', 'Agrumes'];
        $formats = ['120g', '200g', '300g'];
        $typesCire = ['soja', 'paraffine', 'cire végétale', 'beeswax'];

        return [
            'reference' => 'BOUG-' . strtoupper(fake()->unique()->bothify('###??')),
            'parfum' => fake()->randomElement($parfums),
            'nom' => fake()->words(2, true),
            'collection' => fake()->optional()->words(1, true),
            'format' => fake()->randomElement($formats),
            'type_cire' => fake()->randomElement($typesCire),
            'temps_brulure' => fake()->numberBetween(20, 60),
            'notes' => fake()->optional()->sentence(),
            'prix' => fake()->randomFloat(2, 15, 50),
            'quantite' => fake()->numberBetween(0, 100),
            'seuil_alerte' => 5,
        ];
    }
>>>>>>> origin/master
}
