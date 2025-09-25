<?php

namespace Database\Factories;

use App\Models\SalesAndTransactions\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesAndTransactions\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => fake()->optional(0.8)->randomElement([1, 2, 3, 4, 5]), // 80% have user, 20% guest
            'session_id' => fake()->uuid(),
            'total_items' => fake()->numberBetween(1, 10),
            'total_amount' => fake()->randomFloat(2, 100, 50000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'status' => fake()->randomElement(['active', 'abandoned', 'converted', 'expired']),
            'expires_at' => fake()->dateTimeBetween('now', '+30 days'),
            'metadata' => [
                'source' => fake()->randomElement(['website', 'mobile_app']),
                'last_activity' => fake()->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
            ],
            'is_active' => fake()->boolean(90),
            'added_by_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'expires_at' => fake()->dateTimeBetween('+1 day', '+7 days'),
        ]);
    }

    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'abandoned',
            'expires_at' => fake()->dateTimeBetween('-1 week', '-1 day'),
        ]);
    }
}
