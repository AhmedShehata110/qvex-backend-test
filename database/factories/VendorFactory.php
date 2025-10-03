<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessName = fake()->company();
        $businessNameAr = fake()->optional(0.7)->company();

        return [
            'user_id' => User::factory()->vendor(),
            'business_name' => $businessName,
            'business_name_ar' => $businessNameAr,
            'slug' => Str::slug($businessName.'-'.fake()->unique()->randomNumber(4)),
            'description' => fake()->optional(0.8)->paragraph(3),
            'description_ar' => fake()->optional(0.6)->paragraph(3),
            'registration_number' => fake()->unique()->regexify('[A-Z0-9]{8,12}'),
            'tax_id' => fake()->optional(0.7)->regexify('[0-9]{9,15}'),
                        'trade_license' => fake()->optional(0.8)->word(),
            'vendor_type' => fake()->randomElement(['dealership', 'rental_company', 'individual', 'service_center']),
            'status' => fake()->randomElement(['pending', 'active', 'suspended', 'rejected']),
            'business_hours' => $this->generateBusinessHours(),
            'services_offered' => $this->generateServicesOffered(),
            'website' => fake()->optional(0.5)->url(),
            'commission_rate' => fake()->randomFloat(2, 2.5, 10.0),
            'total_sales' => fake()->numberBetween(0, 500),
            'total_revenue' => fake()->randomFloat(2, 0, 1000000),
            'rating_average' => fake()->randomFloat(2, 0, 5),
            'rating_count' => fake()->numberBetween(0, 200),
            'is_featured' => fake()->boolean(15), // 15% chance
            'is_verified' => fake()->boolean(70), // 70% chance
            'verified_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'verified_by' => null, // Will be set by relationships if needed
            'subscription_expires_at' => fake()->optional(0.8)->dateTimeBetween('now', '+2 years'),
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Vendor $vendor) {
            // Set verified_by if verified
            if ($vendor->is_verified && $vendor->verified_at) {
                $adminUser = User::where('email', 'admin@qvex.com')->first();
                if ($adminUser) {
                    $vendor->update(['verified_by' => $adminUser->id]);
                }
            }
        });
    }

    /**
     * Generate business hours JSON structure.
     */
    private function generateBusinessHours(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $hours = [];

        foreach ($days as $day) {
            if (fake()->boolean(85)) { // 85% chance of being open
                $hours[$day] = [
                    'open' => fake()->randomElement(['08:00', '09:00', '10:00']),
                    'close' => fake()->randomElement(['17:00', '18:00', '19:00', '20:00']),
                    'is_open' => true,
                ];
            } else {
                $hours[$day] = [
                    'open' => null,
                    'close' => null,
                    'is_open' => false,
                ];
            }
        }

        return $hours;
    }

    /**
     * Generate services offered JSON structure.
     */
    private function generateServicesOffered(): array
    {
        $allServices = [
            'vehicle_sales',
            'vehicle_rental',
            'vehicle_leasing',
            'maintenance_service',
            'inspection_service',
            'insurance_assistance',
            'financing_assistance',
            'warranty_service',
            'parts_service',
            'towing_service',
        ];

        return fake()->randomElements($allServices, fake()->numberBetween(2, 6));
    }

    /**
     * Indicate that the vendor should be active and verified.
     */
    public function activeAndVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'subscription_expires_at' => fake()->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Indicate that the vendor should be a dealership.
     */
    public function dealership(): static
    {
        return $this->state(fn (array $attributes) => [
            'vendor_type' => 'dealership',
            'services_offered' => ['vehicle_sales', 'financing_assistance', 'warranty_service', 'parts_service'],
        ]);
    }

    /**
     * Indicate that the vendor should be a rental company.
     */
    public function rentalCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'vendor_type' => 'rental_company',
            'services_offered' => ['vehicle_rental', 'vehicle_leasing', 'insurance_assistance'],
        ]);
    }

    /**
     * Indicate that the vendor should be an individual seller.
     */
    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'vendor_type' => 'individual',
            'business_name' => fake()->name()."'s Motors",
            'services_offered' => ['vehicle_sales'],
            'commission_rate' => fake()->randomFloat(2, 3.0, 7.0),
        ]);
    }

    /**
     * Indicate that the vendor should be a service center.
     */
    public function serviceCenter(): static
    {
        return $this->state(fn (array $attributes) => [
            'vendor_type' => 'service_center',
            'services_offered' => ['maintenance_service', 'inspection_service', 'parts_service', 'towing_service'],
        ]);
    }

    /**
     * Indicate that the vendor should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'active',
            'is_verified' => true,
            'rating_average' => fake()->randomFloat(2, 4.0, 5.0),
            'rating_count' => fake()->numberBetween(50, 500),
        ]);
    }

    /**
     * Indicate that the vendor should be suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the vendor should be pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    /**
     * Indicate that the vendor should have high ratings.
     */
    public function highRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_average' => fake()->randomFloat(2, 4.2, 5.0),
            'rating_count' => fake()->numberBetween(20, 200),
            'total_sales' => fake()->numberBetween(50, 500),
            'total_revenue' => fake()->randomFloat(2, 100000, 2000000),
        ]);
    }
}
