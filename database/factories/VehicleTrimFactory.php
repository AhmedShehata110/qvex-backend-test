<?php

namespace Database\Factories;

use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\VehicleTrim>
 */
class VehicleTrimFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleTrim::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $trimNames = [
            'Base', 'L', 'LE', 'XLE', 'Limited', 'Platinum', 'Premium',
            'S', 'SE', 'SEL', 'SEL Plus', 'Titanium', 'ST',
            'LX', 'EX', 'EX-L', 'Touring', 'Sport',
            'SV', 'SL', 'SR', 'Midnight Edition',
            '320i', '330i', '340i', 'M340i', 'M3',
            'C300', 'C43 AMG', 'C63 AMG',
            'Premium', 'Premium Plus', 'Prestige', 'S Line',
        ];

        $engineSizes = [
            '1.6L', '1.8L', '2.0L', '2.4L', '2.5L', '3.0L', '3.5L', '4.0L', '5.0L', '6.0L',
        ];

        $year = fake()->numberBetween(2015, 2024);

        return [
            'model_id' => VehicleModel::factory(),
            'name' => fake()->randomElement($trimNames),
            'name_ar' => fake()->optional(0.6)->words(2, true),
            'year' => $year,
            'engine_size' => fake()->randomElement($engineSizes),
            'fuel_type' => fake()->randomElement(['gasoline', 'diesel', 'hybrid', 'electric', 'cng', 'lpg']),
            'transmission' => fake()->randomElement(['manual', 'automatic', 'cvt', 'dual_clutch']),
            'drivetrain' => fake()->randomElement(['FWD', 'RWD', 'AWD', '4WD']),
            'horsepower' => fake()->numberBetween(150, 500),
            'fuel_consumption_city' => fake()->randomFloat(1, 6.0, 15.0),
            'fuel_consumption_highway' => fake()->randomFloat(1, 5.0, 12.0),
            'seating_capacity' => fake()->randomElement([2, 4, 5, 7, 8]),
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Indicate that the trim should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the trim should be gasoline-powered.
     */
    public function gasoline(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => 'gasoline',
            'fuel_consumption_city' => fake()->randomFloat(1, 8.0, 15.0),
            'fuel_consumption_highway' => fake()->randomFloat(1, 6.0, 12.0),
        ]);
    }

    /**
     * Indicate that the trim should be electric.
     */
    public function electric(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => 'electric',
            'engine_size' => null,
            'fuel_consumption_city' => null,
            'fuel_consumption_highway' => null,
        ]);
    }

    /**
     * Indicate that the trim should be hybrid.
     */
    public function hybrid(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => 'hybrid',
            'fuel_consumption_city' => fake()->randomFloat(1, 4.0, 8.0),
            'fuel_consumption_highway' => fake()->randomFloat(1, 3.5, 7.0),
        ]);
    }

    /**
     * Indicate that the trim should have automatic transmission.
     */
    public function automatic(): static
    {
        return $this->state(fn (array $attributes) => [
            'transmission' => 'automatic',
        ]);
    }

    /**
     * Indicate that the trim should have manual transmission.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'transmission' => 'manual',
        ]);
    }

    /**
     * Indicate that the trim should be all-wheel drive.
     */
    public function awd(): static
    {
        return $this->state(fn (array $attributes) => [
            'drivetrain' => 'AWD',
        ]);
    }

    /**
     * Indicate that the trim should be high-performance.
     */
    public function performance(): static
    {
        $performanceTrims = ['M3', 'C63 AMG', 'S Line', 'ST', 'Sport', 'SR'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($performanceTrims),
            'horsepower' => fake()->numberBetween(300, 600),
            'fuel_consumption_city' => fake()->randomFloat(1, 10.0, 18.0),
            'fuel_consumption_highway' => fake()->randomFloat(1, 8.0, 14.0),
        ]);
    }

    /**
     * Indicate that the trim should be fuel-efficient.
     */
    public function efficient(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => fake()->randomElement(['gasoline', 'hybrid']),
            'horsepower' => fake()->numberBetween(150, 250),
            'fuel_consumption_city' => fake()->randomFloat(1, 4.0, 8.0),
            'fuel_consumption_highway' => fake()->randomFloat(1, 3.5, 6.5),
        ]);
    }

    /**
     * Indicate that the trim should be a luxury variant.
     */
    public function luxury(): static
    {
        $luxuryTrims = ['Limited', 'Platinum', 'Premium Plus', 'Prestige', 'Touring'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($luxuryTrims),
            'seating_capacity' => fake()->randomElement([4, 5]),
        ]);
    }
}
