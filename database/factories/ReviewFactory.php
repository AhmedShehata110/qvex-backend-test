<?php

namespace Database\Factories;

use App\Models\Communication\Review;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Communication\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);

        return [
            'reviewer_id' => User::factory(),
            'reviewable_type' => fake()->randomElement([Vehicle::class, Vendor::class]),
            'reviewable_id' => 1, // Will be set by relationships
            'transaction_id' => Transaction::factory(),
            'rating' => $rating,
            'title' => fake()->sentence(3),
            'comment' => fake()->paragraph(2),
            'rating_breakdown' => $this->generateRatingBreakdown(),
            'is_verified' => fake()->boolean(80), // 80% verified
            'is_anonymous' => fake()->boolean(20), // 20% anonymous
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => fake()->optional(0.2)->sentence(10),
            'helpful_count' => fake()->numberBetween(0, 50),
            'unhelpful_count' => fake()->numberBetween(0, 10),
            'approved_at' => fake()->optional(0.8)->dateTimeBetween('-6 months', 'now'),
            'approved_by' => null, // Will be set by relationships if needed
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Generate rating breakdown JSON.
     */
    private function generateRatingBreakdown(): array
    {
        return [
            'overall' => fake()->numberBetween(1, 5),
            'quality' => fake()->numberBetween(1, 5),
            'service' => fake()->numberBetween(1, 5),
            'communication' => fake()->numberBetween(1, 5),
            'value_for_money' => fake()->numberBetween(1, 5),
        ];
    }

    /**
     * Indicate that the review should be approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the review should be highly rated.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'title' => fake()->randomElement([
                'Great experience!', 'Highly recommended!', 'Excellent service',
                'Very satisfied', 'Outstanding quality',
            ]),
            'rating_breakdown' => [
                'overall' => fake()->numberBetween(4, 5),
                'quality' => fake()->numberBetween(4, 5),
                'service' => fake()->numberBetween(4, 5),
                'communication' => fake()->numberBetween(4, 5),
                'value_for_money' => fake()->numberBetween(4, 5),
            ],
        ]);
    }

    /**
     * Indicate that the review should be low-rated.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'title' => fake()->randomElement([
                'Poor experience', 'Not satisfied', 'Disappointing service',
                'Would not recommend', 'Below expectations',
            ]),
            'rating_breakdown' => [
                'overall' => fake()->numberBetween(1, 2),
                'quality' => fake()->numberBetween(1, 3),
                'service' => fake()->numberBetween(1, 3),
                'communication' => fake()->numberBetween(1, 3),
                'value_for_money' => fake()->numberBetween(1, 3),
            ],
        ]);
    }

    /**
     * Indicate that the review should be for a vehicle.
     */
    public function forVehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewable_type' => Vehicle::class,
        ]);
    }

    /**
     * Indicate that the review should be for a vendor.
     */
    public function forVendor(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewable_type' => Vendor::class,
        ]);
    }

    /**
     * Indicate that the review should be verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'status' => 'approved',
        ]);
    }
}
