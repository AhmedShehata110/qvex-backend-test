<?php

namespace Database\Factories;

use App\Models\Communication\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Communication\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['open', 'in_progress', 'waiting_for_user', 'resolved', 'closed']);
        $createdAt = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'user_id' => User::factory(),
            'subject' => fake()->sentence(5),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['technical', 'billing', 'account', 'feature_request', 'bug_report', 'general']),
            'priority' => fake()->numberBetween(1, 5),
            'status' => $status,
            'assigned_to' => fake()->optional(0.6)->randomElement([1, 2, 3]), // Optional admin user ID
            'last_reply_at' => fake()->optional(0.8)->dateTimeBetween($createdAt, 'now'),
            'resolved_at' => in_array($status, ['resolved', 'closed']) ? fake()->dateTimeBetween($createdAt, 'now') : null,
            'resolution_notes' => in_array($status, ['resolved', 'closed']) ? fake()->optional(0.7)->paragraph() : null,
            'tags' => fake()->randomElements(['urgent', 'vip', 'follow_up', 'escalated', 'refund'], fake()->numberBetween(0, 3)),
            'metadata' => [
                'browser' => fake()->userAgent(),
                'ip_address' => fake()->ipv4(),
                'source' => fake()->randomElement(['web', 'mobile', 'email', 'phone']),
                'attachments_count' => fake()->numberBetween(0, 5),
            ],
            'is_active' => ! in_array($status, ['closed']),
            'added_by_id' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'resolved_at' => null,
            'resolution_notes' => null,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'resolution_notes' => fake()->paragraph(),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => fake()->numberBetween(4, 5),
        ]);
    }
}
