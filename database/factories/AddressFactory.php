<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Maison', 'Domicile', 'Bureau', 'Appartement', 'Autre']),
            'nom' => fake()->name(),
            'email' => fake()->email(),
            'telephone' => fake()->phoneNumber(),
            'adresse' => fake()->streetAddress(),
            'code_postal' => fake()->postcode(),
            'ville' => fake()->city(),
            'pays' => fake()->countryCode(),
            'instructions' => fake()->optional()->sentence(),
            'is_default' => false,
        ];
    }

    /**
     * État: Adresse par défaut
     */
    public function default(): static
    {
        return $this->state(fn () => [
            'is_default' => true,
        ]);
    }
}