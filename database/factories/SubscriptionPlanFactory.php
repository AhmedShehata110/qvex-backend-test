<?php

namespace Database\Factories;

use App\Models\Vendor\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planNames = ['Basic', 'Standard', 'Premium', 'Enterprise', 'Starter', 'Professional', 'Ultimate'];
        $name = fake()->randomElement($planNames);

        return [
            'name' => $name,
            'name_ar' => fake()->optional(0.7)->words(2, true),
            'slug' => Str::slug($name.'-'.fake()->unique()->randomNumber(3)),
            'description' => fake()->paragraph(2),
            'description_ar' => fake()->optional(0.7)->paragraph(2),
            'price' => fake()->randomFloat(2, 29.99, 999.99),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'max_listings' => fake()->randomElement([5, 10, 25, 50, 100, 500, -1]), // -1 for unlimited
            'max_featured_listings' => fake()->randomElement([0, 1, 3, 5, 10, 25]),
            'features' => $this->generateFeatures(),
            'is_active' => fake()->boolean(90),
            'added_by_id' => null,
            'deleted_at' => null,
            'is_popular' => fake()->boolean(20), // 20% chance
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Generate features JSON structure.
     */
    private function generateFeatures(): array
    {
        $allFeatures = [
            'priority_support',
            'analytics_dashboard',
            'advanced_search_visibility',
            'multiple_images',
            'video_uploads',
            'virtual_tours',
            'social_media_integration',
            'lead_management',
            'inventory_management',
            'custom_branding',
            'api_access',
            'bulk_import',
            'automated_reposting',
            'premium_placement',
            'featured_badge',
        ];

        return fake()->randomElements($allFeatures, fake()->numberBetween(3, 10));
    }

    /**
     * Indicate that the plan should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the plan should be popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the plan should be a basic plan.
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic',
            'slug' => 'basic',
            'price' => fake()->randomFloat(2, 19.99, 49.99),
            'max_listings' => fake()->randomElement([5, 10]),
            'max_featured_listings' => fake()->randomElement([0, 1]),
            'features' => ['multiple_images', 'basic_analytics'],
            'sort_order' => 10,
        ]);
    }

    /**
     * Indicate that the plan should be a standard plan.
     */
    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Standard',
            'slug' => 'standard',
            'price' => fake()->randomFloat(2, 49.99, 99.99),
            'max_listings' => fake()->randomElement([25, 50]),
            'max_featured_listings' => fake()->randomElement([3, 5]),
            'features' => [
                'multiple_images',
                'video_uploads',
                'analytics_dashboard',
                'priority_support',
                'social_media_integration',
            ],
            'sort_order' => 20,
            'is_popular' => fake()->boolean(60),
        ]);
    }

    /**
     * Indicate that the plan should be a premium plan.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => fake()->randomFloat(2, 99.99, 199.99),
            'max_listings' => fake()->randomElement([100, 500]),
            'max_featured_listings' => fake()->randomElement([10, 25]),
            'features' => [
                'multiple_images',
                'video_uploads',
                'virtual_tours',
                'analytics_dashboard',
                'priority_support',
                'social_media_integration',
                'lead_management',
                'inventory_management',
                'premium_placement',
                'featured_badge',
            ],
            'sort_order' => 30,
        ]);
    }

    /**
     * Indicate that the plan should be an enterprise plan.
     */
    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'price' => fake()->randomFloat(2, 299.99, 999.99),
            'max_listings' => -1, // Unlimited
            'max_featured_listings' => -1, // Unlimited
            'features' => [
                'multiple_images',
                'video_uploads',
                'virtual_tours',
                'analytics_dashboard',
                'priority_support',
                'social_media_integration',
                'lead_management',
                'inventory_management',
                'custom_branding',
                'api_access',
                'bulk_import',
                'automated_reposting',
                'premium_placement',
                'featured_badge',
            ],
            'sort_order' => 40,
        ]);
    }

    /**
     * Indicate that the plan should be monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_cycle' => 'monthly',
        ]);
    }

    /**
     * Indicate that the plan should be yearly.
     */
    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_cycle' => 'yearly',
            'price' => $attributes['price'] * 10, // Roughly 2 months free
        ]);
    }

    /**
     * Indicate that the plan should be free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0.00,
            'max_listings' => 3,
            'max_featured_listings' => 0,
            'features' => ['multiple_images'],
            'sort_order' => 0,
        ]);
    }
}
