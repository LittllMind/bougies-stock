<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        
        return [
            'numero_commande' => 'CMD-' . now()->year . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'nom' => $user->name ?? fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => $user->email ?? fake()->email(),
            'telephone' => fake()->phoneNumber(),
            'adresse' => fake()->streetAddress(),
            'code_postal' => fake()->postcode(),
            'ville' => fake()->city(),
            'total' => fake()->randomFloat(2, 20, 200),
            'statut' => fake()->randomElement(['en_attente', 'en_preparation', 'prete', 'livree', 'annulee']),
        ];
    }

    /**
     * État : En attente
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'statut' => 'en_attente',
        ]);
    }

    /**
     * État : Payée (statut en_preparation)
     */
    public function paid(): static
    {
        return $this->state(fn () => [
            'statut' => 'en_preparation',
        ]);
    }

    /**
     * État : Prête
     */
    public function ready(): static
    {
        return $this->state(fn () => [
            'statut' => 'prete',
        ]);
    }

    /**
     * État : Livrée
     */
    public function delivered(): static
    {
        return $this->state(fn () => [
            'statut' => 'livree',
        ]);
    }

    /**
     * État : Annulée
     */
    public function cancelled(): static
    {
        return $this->state(fn () => [
            'statut' => 'annulee',
        ]);
    }
}