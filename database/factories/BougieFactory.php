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
        $typesForme = ['sculptée', 'en pot', 'pilier', 'tsuba', 'en cône'];
        $parfums = ['Vanille', 'Lavande', 'Rose', 'Santal', 'Cèdre', "Fleur d'oranger", "Bois d'olivier", 'Miel', 'Citronnelle', 'Amande', 'Pomme cannelle'];
        $collections = ["Collection Nature", "Édition Fêtes", 'Collection Hiver', "Édition Limitée", "Collection Artisanale"];
        $typesCire = ['cire végétale', 'cire de colza', 'cire végétale premium'];
        
        $parfum = $this->faker->randomElement($parfums);
        $typeForme = $this->faker->randomElement($typesForme);
        
        // Génération réaliste du nom
        $nom = match($typeForme) {
            'sculpture' => "Sculpture {$parfum}",
            'chandelle' => "Chandelle {$parfum}",
            'en pot' => "Bougie en pot {$parfum}",
            'pilier' => "Pilier {$parfum}",
            'tsuba' => "Tsuba {$parfum}",
            'en cône' => "Cône {$parfum}",
            default => "Bougie {$parfum}",
        };

        return [
            'reference' => 'BOUG-' . strtoupper(substr(uniqid(), -6)),
            'slug' => null, // Auto-généré par le modèle
            'parfum' => $parfum,
            'nom' => $nom,
            'image' => null, // Sera défini dans le seeder
            'collection' => $this->faker->randomElement($collections),
            'format' => $this->faker->randomElement(['120g', '200g', '250g']),
            'type_cire' => $this->faker->randomElement($typesCire),
            'temps_brulure' => $this->faker->randomElement([20, 35, 45, 60]),
            'notes' => "Bougie {$this->faker->randomElement(['artisanale', 'décorative', 'parfumée', 'naturelle'])} fabriquée à la main en France.",
            'prix' => $this->faker->randomFloat(2, 18, 42),
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

    public function avecImage(string $imageName): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => $imageName,
        ]);
    }
}
