<?php

namespace Database\Factories;

use App\Models\Vehicle\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\Color>
 */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition(): array
    {
        $colorName = fake()->colorName();
        $hexCode = fake()->hexColor();

        return [
            'name' => $colorName,
            'hex_code' => $hexCode,
            'rgb_value' => [
                'r' => hexdec(substr($hexCode, 1, 2)),
                'g' => hexdec(substr($hexCode, 3, 2)),
                'b' => hexdec(substr($hexCode, 5, 2)),
            ],
            'type' => fake()->randomElement(['solid', 'metallic', 'pearl', 'matte']),
            'is_metallic' => fake()->boolean(30), // 30% metallic
            'is_popular' => fake()->boolean(60), // 60% popular
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function metallic(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_metallic' => true,
            'type' => 'metallic',
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
            'sort_order' => fake()->numberBetween(1, 20),
        ]);
    }
}