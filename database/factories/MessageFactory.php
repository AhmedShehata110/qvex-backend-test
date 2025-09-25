<?php

namespace Database\Factories;

use App\Models\Communication\Message;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Communication\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'thread_id' => 'thread-'.fake()->unique()->uuid(),
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'message' => fake()->paragraph(3),
            'type' => fake()->randomElement(['inquiry', 'negotiation', 'support', 'general']),
            'attachments' => fake()->optional(0.2)->randomElements([
                ['type' => 'image', 'url' => fake()->imageUrl()],
                ['type' => 'document', 'url' => fake()->url()],
            ], fake()->numberBetween(1, 3)),
            'metadata' => [
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'timestamp' => fake()->dateTimeThisMonth()->toISOString(),
            ],
            'read_at' => fake()->optional(0.6)->dateTimeBetween('-1 week', 'now'),
            'is_system_message' => fake()->boolean(10), // 10% system messages
            'is_active' => fake()->boolean(98),
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    public function inquiry(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'inquiry',
            'message' => fake()->randomElement([
                'Hi, I\'m interested in this vehicle. Is it still available?',
                'Can I schedule a test drive for this car?',
                'What\'s the best price you can offer for this vehicle?',
                'Does this car come with a warranty?',
                'Can you provide more details about the vehicle history?',
            ]),
        ]);
    }

    public function systemMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system_message' => true,
            'type' => 'support',
            'message' => fake()->randomElement([
                'Your inquiry has been sent to the seller.',
                'The seller has responded to your message.',
                'Your test drive has been scheduled.',
                'Payment has been processed successfully.',
                'Transaction has been completed.',
            ]),
            'sender_id' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }
}
