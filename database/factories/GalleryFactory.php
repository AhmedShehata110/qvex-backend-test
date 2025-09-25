<?php

namespace Database\Factories;

use App\Models\Vehicle\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional(0.7)->paragraph(),
            'vehicle_id' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(['exterior', 'interior', 'engine', 'features', 'documents']),
            'is_featured' => fake()->boolean(20), // 20% featured
            'sort_order' => fake()->numberBetween(1, 100),
            'metadata' => [
                'image_count' => fake()->numberBetween(1, 20),
                'primary_image' => fake()->imageUrl(800, 600, 'cars'),
                'alt_text' => fake()->sentence(),
            ],
            'is_active' => fake()->boolean(90),
            'added_by_id' => null,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ]);
    }

    public function exterior(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'exterior',
        ]);
    }
}