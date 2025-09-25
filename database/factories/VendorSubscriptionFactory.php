<?php

namespace Database\Factories;

use App\Models\Vendor\SubscriptionPlan;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor\VendorSubscription>
 */
class VendorSubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VendorSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 year', 'now');
        $endsAt = fake()->dateTimeBetween($startsAt, '+1 year');

        return [
            'vendor_id' => Vendor::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'amount_paid' => fake()->randomFloat(2, 29.99, 999.99),
            'currency' => fake()->randomElement(['USD', 'EUR', 'AED', 'SAR']),
            'status' => fake()->randomElement(['active', 'cancelled', 'expired', 'suspended']),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'cancelled_at' => null,
            'listings_used' => fake()->numberBetween(0, 50),
            'featured_listings_used' => fake()->numberBetween(0, 10),
            'auto_renewal' => fake()->boolean(70), // 70% have auto-renewal
            'payment_reference' => fake()->optional(0.9)->regexify('PAY[0-9]{10}'),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (VendorSubscription $subscription) {
            // Set cancelled_at if status is cancelled
            if ($subscription->status === 'cancelled') {
                $subscription->cancelled_at = fake()->dateTimeBetween($subscription->starts_at, $subscription->ends_at);
            }
        });
    }

    /**
     * Indicate that the subscription should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'starts_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'ends_at' => fake()->dateTimeBetween('now', '+1 year'),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the subscription should be expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'starts_at' => fake()->dateTimeBetween('-2 years', '-6 months'),
            'ends_at' => fake()->dateTimeBetween('-6 months', '-1 month'),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the subscription should be cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            $startsAt = fake()->dateTimeBetween('-1 year', '-3 months');
            $endsAt = fake()->dateTimeBetween($startsAt, '+6 months');
            $cancelledAt = fake()->dateTimeBetween($startsAt, min($endsAt, now()));

            return [
                'status' => 'cancelled',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'cancelled_at' => $cancelledAt,
                'auto_renewal' => false,
            ];
        });
    }

    /**
     * Indicate that the subscription should be for a basic plan.
     */
    public function basicPlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_paid' => fake()->randomFloat(2, 19.99, 49.99),
            'listings_used' => fake()->numberBetween(0, 10),
            'featured_listings_used' => fake()->numberBetween(0, 1),
        ]);
    }

    /**
     * Indicate that the subscription should be for a premium plan.
     */
    public function premiumPlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_paid' => fake()->randomFloat(2, 99.99, 199.99),
            'listings_used' => fake()->numberBetween(0, 100),
            'featured_listings_used' => fake()->numberBetween(0, 25),
        ]);
    }

    /**
     * Indicate that the subscription should be heavily used.
     */
    public function heavilyUsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'listings_used' => fake()->numberBetween(80, 500),
            'featured_listings_used' => fake()->numberBetween(15, 50),
        ]);
    }

    /**
     * Indicate that the subscription should be recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'ends_at' => fake()->dateTimeBetween('now', '+9 months'),
        ]);
    }

    /**
     * Indicate that the subscription should have auto-renewal enabled.
     */
    public function autoRenewal(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_renewal' => true,
            'status' => 'active',
        ]);
    }
}
