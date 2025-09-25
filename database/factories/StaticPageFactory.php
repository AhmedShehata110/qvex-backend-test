<?php

namespace Database\Factories;

use App\Models\Content\StaticPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\StaticPage>
 */
class StaticPageFactory extends Factory
{
    protected $model = StaticPage::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(),
            'meta_title' => fake()->optional(0.8)->sentence(6),
            'meta_description' => fake()->optional(0.8)->paragraph(),
            'is_published' => fake()->boolean(80), // 80% published
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'template' => fake()->randomElement(['default', 'full_width', 'sidebar', 'landing']),
            'order' => fake()->numberBetween(1, 100),
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}