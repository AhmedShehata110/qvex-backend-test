<?php

namespace Database\Factories;

use App\Models\SalesAndTransactions\Cart;
use App\Models\SalesAndTransactions\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesAndTransactions\CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10, 1000);

        return [
            'cart_id' => Cart::factory(),
            'product_id' => fake()->numberBetween(1, 100), // Assuming product IDs from 1-100
            'product_type' => fake()->randomElement(['vehicle', 'part', 'accessory', 'service']),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'options' => [
                'color' => fake()->optional(0.5)->colorName(),
                'size' => fake()->optional(0.3)->randomElement(['S', 'M', 'L', 'XL']),
                'warranty' => fake()->optional(0.4)->randomElement(['1_year', '2_years', '3_years']),
            ],
            'metadata' => [
                'added_at' => fake()->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
                'source' => fake()->randomElement(['search', 'recommendation', 'direct']),
            ],
            'is_active' => fake()->boolean(98),
            'added_by_id' => null,
        ];
    }

    public function vehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'vehicle',
            'unit_price' => fake()->randomFloat(2, 5000, 50000),
            'options' => [
                'color' => fake()->colorName(),
                'transmission' => fake()->randomElement(['manual', 'automatic']),
                'fuel_type' => fake()->randomElement(['petrol', 'diesel', 'electric', 'hybrid']),
            ],
        ]);
    }

    public function accessory(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'accessory',
            'unit_price' => fake()->randomFloat(2, 10, 500),
            'options' => [
                'color' => fake()->optional(0.7)->colorName(),
                'material' => fake()->randomElement(['leather', 'fabric', 'plastic', 'metal']),
            ],
        ]);
    }
}
