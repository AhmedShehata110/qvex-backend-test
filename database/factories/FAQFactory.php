<?php

namespace Database\Factories;

use App\Models\Content\FAQ;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\FAQ>
 */
class FAQFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FAQ::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'general', 'buying', 'selling', 'renting', 'payments',
            'account', 'technical', 'policies', 'support',
        ];

        return [
            'question' => fake()->sentence().'?',
            'question_ar' => fake()->optional(0.7)->sentence().'؟',
            'answer' => fake()->paragraph(3),
            'answer_ar' => fake()->optional(0.7)->paragraph(2),
            'category' => fake()->randomElement($categories),
            'is_active' => fake()->boolean(95),
            'added_by_id' => User::factory(),
            'deleted_at' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'view_count' => fake()->numberBetween(0, 500),
        ];
    }

    /**
     * Indicate that the FAQ should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the FAQ should be about buying.
     */
    public function buying(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'buying',
            'question' => fake()->randomElement([
                'How do I buy a vehicle on the platform?',
                'What payment methods are accepted?',
                'Is there a warranty on vehicles?',
                'Can I test drive before buying?',
                'How do I verify the vehicle condition?',
            ]),
        ]);
    }

    /**
     * Indicate that the FAQ should be about selling.
     */
    public function selling(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'selling',
            'question' => fake()->randomElement([
                'How do I list my vehicle for sale?',
                'What commission does the platform charge?',
                'How long does it take to sell a vehicle?',
                'Can I edit my listing after posting?',
                'How do I handle inquiries from buyers?',
            ]),
        ]);
    }

    /**
     * Indicate that the FAQ should be about account management.
     */
    public function account(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'account',
            'question' => fake()->randomElement([
                'How do I create an account?',
                'How do I reset my password?',
                'Can I change my email address?',
                'How do I verify my phone number?',
                'How do I delete my account?',
            ]),
        ]);
    }

    /**
     * Indicate that the FAQ should be popular/frequently viewed.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(100, 1000),
            'sort_order' => fake()->numberBetween(0, 20), // Higher priority
        ]);
    }
}
