<?php

namespace Database\Factories;

use App\Models\Communication\ContactUs;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Communication\ContactUs>
 */
class ContactUsFactory extends Factory
{
    protected $model = ContactUs::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraphs(2, true),
            'category' => fake()->randomElement(['general', 'support', 'complaint', 'suggestion', 'partnership', 'technical']),
            'priority' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(['pending', 'in_progress', 'resolved', 'closed']),
            'assigned_to' => fake()->optional(0.3)->randomElement([1, 2, 3]), // Optional admin user ID
            'response' => fake()->optional(0.4)->paragraph(),
            'responded_at' => fake()->optional(0.4)->dateTimeBetween('-1 week', 'now'),
            'responded_by' => fake()->optional(0.4)->randomElement([1, 2, 3]),
            'metadata' => [
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'source' => fake()->randomElement(['website', 'mobile_app', 'email']),
            ],
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'assigned_to' => null,
            'response' => null,
            'responded_at' => null,
            'responded_by' => null,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'response' => fake()->paragraph(),
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'responded_by' => fake()->randomElement([1, 2, 3]),
        ]);
    }
}
