<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 20, 200),
            'montant' => fake()->randomFloat(2, 20, 200),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'stripe_session_id' => 'cs_test_' . fake()->uuid(),
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
        ];
    }

    /**
     * État : Paiement complété
     */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * État : Paiement Stripe
     */
    public function stripe(): static
    {
        return $this->state(fn () => [
            'stripe_payment_intent_id' => 'pi_' . fake()->uuid(),
        ]);
    }
}
