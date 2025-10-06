<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\Brand;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $condition = fake()->randomElement(['new', 'used', 'certified_preowned', 'salvage']);
        $availabilityType = fake()->randomElement(['sale', 'rent', 'both']);
        $year = fake()->numberBetween(2010, 2024);

        // Generate title based on make/model (will be updated in configure)
        $title = fake()->words(3, true).' '.$year;

        // Generate price based on year and condition
        $basePrice = $this->calculateBasePrice($year, $condition);

        return [
            'vendor_id' => Vendor::factory(),
            'brand_id' => Brand::factory(),
            'model_id' => VehicleModel::factory(),
            'trim_id' => VehicleTrim::factory(),
            'vin' => fake()->optional(0.8)->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'year' => $year,
            'title' => $title,
            'title_ar' => fake()->optional(0.6)->words(3, true),
            'description' => fake()->optional(0.9)->paragraph(3),
            'description_ar' => fake()->optional(0.6)->paragraph(2),
            'condition' => $condition,
            'availability_type' => $availabilityType,
            'status' => fake()->randomElement(['draft', 'active', 'sold', 'rented', 'inactive', 'pending_approval']),
            'price' => $basePrice,
            'original_price' => fake()->optional(0.3)->randomFloat(2, $basePrice, $basePrice * 1.2),
            'is_negotiable' => fake()->boolean(80), // 80% negotiable
            'rental_daily_rate' => $availabilityType !== 'sale' ? fake()->randomFloat(2, 50, 500) : null,
            'rental_weekly_rate' => $availabilityType !== 'sale' ? fake()->randomFloat(2, 300, 3000) : null,
            'rental_monthly_rate' => $availabilityType !== 'sale' ? fake()->randomFloat(2, 1200, 12000) : null,
            'security_deposit' => $availabilityType !== 'sale' ? fake()->randomFloat(2, 500, 5000) : null,
            'mileage' => $condition === 'new' ? fake()->numberBetween(0, 100) : fake()->numberBetween(1000, 300000),
            'mileage_unit' => fake()->randomElement(['km', 'miles']),
            'exterior_color' => fake()->randomElement([
                'White', 'Black', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Brown', 'Gold', 'Orange',
            ]),
            'interior_color' => fake()->randomElement([
                'Black', 'Beige', 'Gray', 'Brown', 'Red', 'Blue',
            ]),
            'doors' => fake()->randomElement([2, 4, 5]),
            'cylinders' => fake()->randomElement([4, 6, 8, 12]),
            'license_plate' => fake()->optional(0.7)->regexify('[A-Z]{2,3}[0-9]{3,4}'),
            'additional_specs' => $this->generateAdditionalSpecs(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->countryCode(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'has_warranty' => fake()->boolean(40),
            'warranty_details' => fake()->optional(0.4)->sentence(10),
            'warranty_expires_at' => fake()->optional(0.4)->dateTimeBetween('now', '+5 years'),
            'last_service_date' => fake()->optional(0.6)->dateTimeBetween('-2 years', 'now'),
            'service_interval_km' => fake()->optional(0.6)->numberBetween(5000, 15000),
            'service_history' => fake()->optional(0.5)->paragraph(2),
            'slug' => Str::slug($title.'-'.fake()->unique()->randomNumber(4)),
            'seo_keywords' => fake()->optional(0.7)->words(5),
            'is_featured' => fake()->boolean(15), // 15% featured
            'is_urgent' => fake()->boolean(10), // 10% urgent
            'featured_until' => fake()->optional(0.15)->dateTimeBetween('now', '+3 months'),
            'view_count' => fake()->numberBetween(0, 1000),
            'inquiry_count' => fake()->numberBetween(0, 50),
            'favorite_count' => fake()->numberBetween(0, 25),
            'approved_at' => fake()->optional(0.8)->dateTimeBetween('-6 months', 'now'),
            'approved_by' => null, // Will be set by relationships if needed
            'rejection_reason' => null,
            'is_active' => fake()->boolean(90),
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Vehicle $vehicle) {
            // Update title to include make/model if they exist
            if ($vehicle->brand && $vehicle->model) {
                $vehicle->title = $vehicle->year.' '.$vehicle->brand->name.' '.$vehicle->model->name;
                if ($vehicle->trim) {
                    $vehicle->title .= ' '.$vehicle->trim->name;
                }
                $vehicle->slug = Str::slug($vehicle->title.'-'.fake()->unique()->randomNumber(4));
            }
        })->afterCreating(function (Vehicle $vehicle) {
            // Set approved_by if approved
            if ($vehicle->approved_at && $vehicle->status === 'active') {
                $adminUser = User::where('email', 'admin@qvex.com')->first();
                if ($adminUser) {
                    $vehicle->update(['approved_by' => $adminUser->id]);
                }
            }
        });
    }

    /**
     * Calculate base price based on year and condition.
     */
    private function calculateBasePrice(int $year, string $condition): float
    {
        $currentYear = date('Y');
        $age = $currentYear - $year;

        // Base price decreases with age
        $basePrice = max(5000, 50000 - ($age * 3000));

        // Adjust for condition
        $conditionMultipliers = [
            'new' => 1.0,
            'certified_preowned' => 0.85,
            'used' => 0.7,
            'salvage' => 0.3,
        ];

        return round($basePrice * ($conditionMultipliers[$condition] ?? 0.7), 2);
    }

    /**
     * Generate additional specifications JSON.
     */
    private function generateAdditionalSpecs(): array
    {
        $specs = [];

        if (fake()->boolean(70)) {
            $specs['engine'] = [
                'displacement' => fake()->randomFloat(1, 1.0, 6.0).'L',
                'configuration' => fake()->randomElement(['Inline-4', 'V6', 'V8', 'Flat-4']),
                'fuel_system' => fake()->randomElement(['Direct Injection', 'Port Injection', 'Turbo']),
            ];
        }

        if (fake()->boolean(80)) {
            $specs['features'] = fake()->randomElements([
                'Air Conditioning', 'Power Windows', 'Power Locks', 'Cruise Control',
                'Bluetooth', 'USB Port', 'Navigation System', 'Backup Camera',
                'Heated Seats', 'Leather Seats', 'Sunroof', 'Premium Audio',
                'Keyless Entry', 'Remote Start', 'Parking Sensors',
            ], fake()->numberBetween(3, 8));
        }

        if (fake()->boolean(60)) {
            $specs['safety'] = fake()->randomElements([
                'ABS', 'Airbags', 'Stability Control', 'Traction Control',
                'Blind Spot Monitoring', 'Lane Departure Warning',
                'Forward Collision Warning', 'Automatic Emergency Braking',
            ], fake()->numberBetween(2, 5));
        }

        return $specs;
    }

    /**
     * Indicate that the vehicle should be new.
     */
    public function newCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => 'new',
            'mileage' => fake()->numberBetween(0, 100),
            'year' => fake()->numberBetween(2022, 2024),
            'has_warranty' => true,
            'warranty_expires_at' => fake()->dateTimeBetween('+1 year', '+5 years'),
        ]);
    }

    /**
     * Indicate that the vehicle should be used.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => 'used',
            'mileage' => fake()->numberBetween(10000, 200000),
            'year' => fake()->numberBetween(2010, 2022),
        ]);
    }

    /**
     * Indicate that the vehicle should be for sale.
     */
    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => 'sale',
            'rental_daily_rate' => null,
            'rental_weekly_rate' => null,
            'rental_monthly_rate' => null,
            'security_deposit' => null,
        ]);
    }

    /**
     * Indicate that the vehicle should be for rent.
     */
    public function forRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => 'rent',
            'rental_daily_rate' => fake()->randomFloat(2, 50, 500),
            'rental_weekly_rate' => fake()->randomFloat(2, 300, 3000),
            'rental_monthly_rate' => fake()->randomFloat(2, 1200, 12000),
            'security_deposit' => fake()->randomFloat(2, 500, 5000),
        ]);
    }

    /**
     * Indicate that the vehicle should be active and approved.
     */
    public function activeAndApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_active' => true,
            'approved_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the vehicle should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => fake()->dateTimeBetween('now', '+3 months'),
            'status' => 'active',
            'view_count' => fake()->numberBetween(100, 2000),
            'inquiry_count' => fake()->numberBetween(10, 100),
            'favorite_count' => fake()->numberBetween(5, 50),
        ]);
    }

    /**
     * Indicate that the vehicle should be sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
            'availability_type' => 'sale',
        ]);
    }

    /**
     * Indicate that the vehicle should be pending approval.
     */
    public function pendingApproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_approval',
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /**
     * Indicate that the vehicle should be luxury.
     */
    public function luxury(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 50000, 200000),
            'condition' => fake()->randomElement(['new', 'used', 'certified_preowned']),
            'interior_color' => fake()->randomElement(['Black', 'Beige', 'Brown']),
            'has_warranty' => true,
        ]);
    }

    /**
     * Indicate that the vehicle should be popular/high-interest.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(500, 3000),
            'inquiry_count' => fake()->numberBetween(20, 100),
            'favorite_count' => fake()->numberBetween(10, 75),
            'is_featured' => fake()->boolean(60),
        ]);
    }
}
