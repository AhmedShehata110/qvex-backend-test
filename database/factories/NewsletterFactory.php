<?php

namespace Database\Factories;

use App\Models\Content\Newsletter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Newsletter>
 */
class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);
        $status = fake()->randomElement(['draft', 'scheduled', 'sent', 'cancelled']);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'subject' => fake()->sentence(6),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(),
            'status' => $status,
            'scheduled_at' => $status === 'scheduled' ? fake()->dateTimeBetween('now', '+1 month') : null,
            'sent_at' => $status === 'sent' ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'recipient_count' => $status === 'sent' ? fake()->numberBetween(100, 10000) : 0,
            'open_rate' => $status === 'sent' ? fake()->randomFloat(2, 5, 50) : 0,
            'click_rate' => $status === 'sent' ? fake()->randomFloat(2, 1, 20) : 0,
            'template_id' => fake()->optional(0.7)->numberBetween(1, 10),
            'tags' => fake()->randomElements(['marketing', 'product', 'announcement', 'educational', 'promotional'], fake()->numberBetween(1, 3)),
            'metadata' => [
                'campaign_id' => fake()->uuid(),
                'segment' => fake()->randomElement(['all_users', 'active_users', 'new_users', 'premium_users']),
                'priority' => fake()->randomElement(['low', 'medium', 'high']),
            ],
            'is_active' => in_array($status, ['draft', 'scheduled']),
            'added_by_id' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
            'recipient_count' => 0,
            'open_rate' => 0,
            'click_rate' => 0,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'recipient_count' => fake()->numberBetween(100, 10000),
            'open_rate' => fake()->randomFloat(2, 5, 50),
            'click_rate' => fake()->randomFloat(2, 1, 20),
        ]);
    }
}