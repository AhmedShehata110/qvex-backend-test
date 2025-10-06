<?php

namespace Database\Factories;

use App\Models\Vehicle\Brand;
use App\Models\Vehicle\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modelNames = [
            'Camry', 'Corolla', 'Prius', 'RAV4', 'Highlander', 'Sienna', 'Tacoma', 'Tundra',
            'Accord', 'Civic', 'CR-V', 'Pilot', 'Ridgeline', 'Odyssey',
            'Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Titan', 'Frontier',
            '3 Series', '5 Series', '7 Series', 'X3', 'X5', 'X7',
            'C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'GLS',
            'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7',
            'Jetta', 'Passat', 'Tiguan', 'Atlas', 'Golf',
            'F-150', 'Explorer', 'Escape', 'Edge', 'Expedition',
            'Silverado', 'Malibu', 'Equinox', 'Tahoe', 'Suburban',
            'Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade',
            'Forte', 'Optima', 'Sorento', 'Telluride', 'Sportage',
        ];

        $name = fake()->randomElement($modelNames);
        $startYear = fake()->numberBetween(1990, 2020);
        $endYear = fake()->optional(0.3)->numberBetween($startYear + 5, 2024);

        return [
            'brand_id' => Brand::factory(),
            'name' => $name,
            'name_ar' => fake()->optional(0.6)->words(2, true),
            'slug' => Str::slug($name),
            'year_start' => $startYear,
            'year_end' => $endYear,
            'body_type' => fake()->randomElement([
                'sedan', 'suv', 'hatchback', 'coupe', 'convertible',
                'wagon', 'pickup', 'van', 'truck', 'other',
            ]),
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
            'deleted_at' => null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the model should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the model should be a sedan.
     */
    public function sedan(): static
    {
        $sedanModels = ['Camry', 'Accord', 'Altima', '3 Series', 'C-Class', 'A4', 'Jetta', 'Malibu', 'Elantra', 'Forte'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($sedanModels),
            'body_type' => 'sedan',
        ]);
    }

    /**
     * Indicate that the model should be an SUV.
     */
    public function suv(): static
    {
        $suvModels = ['RAV4', 'CR-V', 'Rogue', 'X5', 'GLE', 'Q5', 'Tiguan', 'Explorer', 'Equinox', 'Tucson'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($suvModels),
            'body_type' => 'suv',
        ]);
    }

    /**
     * Indicate that the model should be a pickup truck.
     */
    public function pickup(): static
    {
        $pickupModels = ['Tacoma', 'Tundra', 'Ridgeline', 'Titan', 'Frontier', 'F-150', 'Silverado'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($pickupModels),
            'body_type' => 'pickup',
        ]);
    }

    /**
     * Indicate that the model should be currently in production.
     */
    public function currentProduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'year_start' => fake()->numberBetween(2010, 2020),
            'year_end' => null, // Still in production
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the model should be discontinued.
     */
    public function discontinued(): static
    {
        $startYear = fake()->numberBetween(1990, 2015);

        return $this->state(fn (array $attributes) => [
            'year_start' => $startYear,
            'year_end' => fake()->numberBetween($startYear + 3, 2020),
        ]);
    }
}
