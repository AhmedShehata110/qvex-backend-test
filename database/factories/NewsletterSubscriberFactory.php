<?php

namespace Database\Factories;

use App\Models\Communication\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Communication\NewsletterSubscriber>
 */
class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->optional(0.8)->name(),
            'is_subscribed' => fake()->boolean(95), // 95% are subscribed
            'subscribed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'unsubscribed_at' => fake()->optional(0.05)->dateTimeBetween('-6 months', 'now'), // 5% unsubscribed
            'subscription_source' => fake()->randomElement(['website', 'social_media', 'referral', 'advertisement', 'newsletter_signup']),
            'preferences' => [
                'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly']),
                'categories' => fake()->randomElements(['vehicles', 'market_updates', 'tips', 'promotions'], fake()->numberBetween(1, 4)),
            ],
            'verification_token' => Str::random(32),
            'is_verified' => fake()->boolean(90), // 90% verified
            'verified_at' => fake()->optional(0.9)->dateTimeBetween('-1 year', 'now'),
            'is_active' => fake()->boolean(98),
            'added_by_id' => null,
        ];
    }

    public function subscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_subscribed' => true,
            'subscribed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'unsubscribed_at' => null,
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_subscribed' => false,
            'unsubscribed_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
