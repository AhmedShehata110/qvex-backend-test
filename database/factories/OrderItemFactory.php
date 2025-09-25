<?php

namespace Database\Factories;

use App\Models\SalesAndTransactions\Order;
use App\Models\SalesAndTransactions\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesAndTransactions\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        $productType = fake()->randomElement(['vehicle', 'part', 'accessory', 'service']);

        return [
            'order_id' => Order::factory(),
            'product_id' => fake()->numberBetween(1, 100),
            'product_type' => $productType,
            'product_name' => $this->generateProductName($productType),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'options' => [
                'color' => fake()->optional(0.5)->colorName(),
                'size' => fake()->optional(0.3)->randomElement(['S', 'M', 'L', 'XL']),
                'warranty' => fake()->optional(0.4)->randomElement(['1_year', '2_years', '3_years']),
            ],
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'metadata' => [
                'weight' => fake()->randomFloat(2, 0.1, 100),
                'dimensions' => [
                    'length' => fake()->randomFloat(2, 10, 200),
                    'width' => fake()->randomFloat(2, 5, 100),
                    'height' => fake()->randomFloat(2, 5, 100),
                ],
                'supplier_id' => fake()->optional(0.6)->numberBetween(1, 20),
            ],
            'is_active' => fake()->boolean(98),
            'added_by_id' => null,
        ];
    }

    private function generateProductName(string $type): string
    {
        return match ($type) {
            'vehicle' => fake()->randomElement([
                'Toyota Camry 2023',
                'Honda Civic 2022',
                'Ford Mustang GT',
                'BMW X5',
                'Mercedes-Benz C-Class',
            ]),
            'part' => fake()->randomElement([
                'Brake Pad Set',
                'Oil Filter',
                'Spark Plugs',
                'Air Filter',
                'Tire Set',
            ]),
            'accessory' => fake()->randomElement([
                'Car Cover',
                'Floor Mats',
                'Phone Mount',
                'Dashboard Camera',
                'Seat Covers',
            ]),
            'service' => fake()->randomElement([
                'Oil Change Service',
                'Tire Rotation',
                'Brake Inspection',
                'Detailing Service',
                'Engine Tune-up',
            ]),
            default => fake()->words(3, true),
        };
    }

    public function vehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'vehicle',
            'unit_price' => fake()->randomFloat(2, 5000, 50000),
            'product_name' => fake()->randomElement(['Toyota Camry', 'Honda Civic', 'Ford Mustang', 'BMW X5']),
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
        ]);
    }
}
