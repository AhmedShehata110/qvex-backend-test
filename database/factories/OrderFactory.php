<?php

namespace Database\Factories;

use App\Models\SalesAndTransactions\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesAndTransactions\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
        $paymentStatus = fake()->randomElement(['pending', 'paid', 'failed', 'refunded']);
        $orderDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.strtoupper(Str::random(8)),
            'total_amount' => fake()->randomFloat(2, 50, 10000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'status' => $status,
            'payment_status' => $paymentStatus,
            'shipping_address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'zip_code' => fake()->postcode(),
                'country' => fake()->country(),
            ],
            'billing_address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'zip_code' => fake()->postcode(),
                'country' => fake()->country(),
            ],
            'order_date' => $orderDate,
            'shipped_at' => in_array($status, ['shipped', 'delivered']) ? fake()->dateTimeBetween($orderDate, 'now') : null,
            'delivered_at' => $status === 'delivered' ? fake()->dateTimeBetween($orderDate, 'now') : null,
            'notes' => fake()->optional(0.3)->paragraph(),
            'metadata' => [
                'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
                'shipping_method' => fake()->randomElement(['standard', 'express', 'overnight']),
                'tax_amount' => fake()->randomFloat(2, 0, 500),
                'discount_amount' => fake()->randomFloat(2, 0, 100),
            ],
            'is_active' => ! in_array($status, ['cancelled']),
            'added_by_id' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipped_at' => null,
            'delivered_at' => null,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'shipped_at' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
            'delivered_at' => fake()->dateTimeBetween('-1 day', 'now'),
        ]);
    }
}
