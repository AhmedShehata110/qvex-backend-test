<?php

namespace Database\Factories;

use App\Models\Marketing\Banner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marketing\Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'title_ar' => fake()->optional(0.7)->sentence(3),
            'description' => fake()->paragraph(2),
            'description_ar' => fake()->optional(0.7)->paragraph(2),
            'image' => fake()->imageUrl(800, 400, 'business'),
            'image_mobile' => fake()->imageUrl(400, 300, 'business'),
            'link_url' => fake()->optional(0.8)->url(),
            'link_text' => fake()->optional(0.8)->words(2, true),
            'link_text_ar' => fake()->optional(0.6)->words(2, true),
            'position' => fake()->randomElement(['header', 'sidebar', 'footer', 'content']),
            'type' => fake()->randomElement(['promotional', 'informational', 'seasonal']),
            'targeting' => $this->generateTargeting(),
            'is_active' => fake()->boolean(85),
            'added_by_id' => User::factory(),
            'deleted_at' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'view_count' => fake()->numberBetween(0, 10000),
            'click_count' => fake()->numberBetween(0, 500),
            'starts_at' => fake()->optional(0.8)->dateTimeBetween('-1 month', 'now'),
            'expires_at' => fake()->optional(0.8)->dateTimeBetween('now', '+6 months'),
        ];
    }

    private function generateTargeting(): array
    {
        return [
            'user_types' => fake()->randomElements(['guest', 'customer', 'vendor'], fake()->numberBetween(1, 3)),
            'locations' => fake()->optional(0.5)->randomElements(['US', 'AE', 'SA'], fake()->numberBetween(1, 2)),
            'devices' => fake()->randomElements(['desktop', 'mobile', 'tablet'], fake()->numberBetween(1, 3)),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'starts_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'expires_at' => fake()->dateTimeBetween('now', '+3 months'),
        ]);
    }

    public function header(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'header',
            'type' => 'promotional',
        ]);
    }
}
