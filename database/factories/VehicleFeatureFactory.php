<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle\VehicleFeature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\VehicleFeature>
 */
class VehicleFeatureFactory extends Factory
{
    protected $model = VehicleFeature::class;

    public function definition(): array
    {
        $features = [
            // Safety Features
            'ABS', 'Airbags', 'Stability Control', 'Traction Control', 'Blind Spot Monitoring',
            'Lane Departure Warning', 'Forward Collision Warning', 'Automatic Emergency Braking',
            'Parking Sensors', 'Backup Camera', 'Rearview Camera',

            // Comfort Features
            'Air Conditioning', 'Climate Control', 'Heated Seats', 'Cooled Seats', 'Leather Seats',
            'Power Windows', 'Power Locks', 'Power Steering', 'Cruise Control', 'Sunroof',
            'Moonroof', 'Keyless Entry', 'Remote Start', 'Push Button Start',

            // Technology Features
            'Bluetooth', 'USB Port', 'Aux Input', 'Navigation System', 'Premium Audio',
            'Satellite Radio', 'Apple CarPlay', 'Android Auto', 'Wireless Charging',

            // Performance Features
            'Turbo', 'Supercharger', 'All-Wheel Drive', '4WD', 'Sport Mode',
            'Eco Mode', 'Paddle Shifters', 'Limited Slip Differential',
        ];

        $name = fake()->randomElement($features);

        return [
            'name' => $name,
            'name_ar' => fake()->optional(0.6)->words(2, true),
            'slug' => Str::slug($name),
            'description' => fake()->optional(0.7)->sentence(8),
            'description_ar' => fake()->optional(0.5)->sentence(6),
            'category' => $this->getFeatureCategory($name),
            'icon' => fake()->optional(0.8)->word(),
            'is_active' => fake()->boolean(95),
            'added_by_id' => User::factory(),
            'deleted_at' => null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    private function getFeatureCategory(string $feature): string
    {
        $safetyFeatures = ['ABS', 'Airbags', 'Stability Control', 'Traction Control', 'Blind Spot Monitoring'];
        $comfortFeatures = ['Air Conditioning', 'Climate Control', 'Heated Seats', 'Power Windows'];
        $technologyFeatures = ['Bluetooth', 'USB Port', 'Navigation System', 'Apple CarPlay'];
        $performanceFeatures = ['Turbo', 'All-Wheel Drive', '4WD', 'Sport Mode'];

        if (in_array($feature, $safetyFeatures)) {
            return 'safety';
        }
        if (in_array($feature, $comfortFeatures)) {
            return 'comfort';
        }
        if (in_array($feature, $technologyFeatures)) {
            return 'technology';
        }
        if (in_array($feature, $performanceFeatures)) {
            return 'performance';
        }

        return fake()->randomElement(['safety', 'comfort', 'technology', 'performance']);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function safety(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'safety',
            'name' => fake()->randomElement(['ABS', 'Airbags', 'Stability Control', 'Backup Camera']),
        ]);
    }

    public function comfort(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'comfort',
            'name' => fake()->randomElement(['Air Conditioning', 'Heated Seats', 'Power Windows', 'Sunroof']),
        ]);
    }
}
