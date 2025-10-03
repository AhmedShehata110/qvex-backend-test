<?php

namespace Database\Factories;

use App\Models\Marketing\Advertisement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marketing\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    protected $model = Advertisement::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+3 months');

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.8)->paragraph(),
            'type' => fake()->randomElement(['banner', 'sidebar', 'popup', 'email', 'social_media']),
            'position' => fake()->randomElement(['header', 'sidebar', 'footer', 'homepage']),
            'target_url' => fake()->optional(0.8)->url(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => fake()->boolean(80), // 80% active
            'click_count' => fake()->numberBetween(0, 1000),
            'view_count' => fake()->numberBetween(100, 10000),
            'budget' => fake()->randomFloat(2, 100, 5000),
            'spent' => fake()->randomFloat(2, 0, 1000),
            'target_audience' => [
                'age_range' => fake()->randomElement(['18-25', '26-35', '36-50', '50+']),
                'interests' => fake()->randomElements(['cars', 'luxury', 'budget', 'family'], fake()->numberBetween(1, 3)),
            ],
            'priority' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(90),
            'added_by_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_date' => fake()->dateTimeBetween('-1 week', '-1 day'),
            'end_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
