<?php

namespace Database\Factories;

use App\Models\Content\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->optional(0.7)->company(),
            'position' => fake()->optional(0.8)->jobTitle(),
            'content' => fake()->paragraphs(2, true),
            'rating' => fake()->numberBetween(3, 5), // Mostly positive ratings
            'is_featured' => fake()->boolean(20), // 20% featured
            'is_approved' => fake()->boolean(85), // 85% approved
            'approved_at' => fake()->optional(0.85)->dateTimeBetween('-6 months', 'now'),
            'approved_by' => fake()->optional(0.85)->numberBetween(1, 5),
            'metadata' => [
                'source' => fake()->randomElement(['website', 'social_media', 'email', 'phone']),
                'verified' => fake()->boolean(90),
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
            ],
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'rating' => 5,
            'is_approved' => true,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'approved_by' => fake()->numberBetween(1, 5),
        ]);
    }

    public function fiveStar(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
        ]);
    }
}