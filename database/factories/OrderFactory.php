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
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'paid', 'preparing', 'ready', 'delivered', 'cancelled']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'total_amount' => fake()->randomFloat(2, 20, 200),
            'shipping_address_id' => null,
        ];
    }

    /**
     * État : En attente
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * État : Payée
     */
    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * État : Prête
     */
    public function ready(): static
    {
        return $this->state(fn () => [
            'status' => 'ready',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * État : Livrée
     */
    public function delivered(): static
    {
        return $this->state(fn () => [
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * État : Annulée
     */
    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }
}
