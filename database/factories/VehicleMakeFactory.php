<?php

namespace Database\Factories;

use App\Models\Vehicle\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle\VehicleMake>
 */
class VehicleMakeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleMake::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Toyota', 'Honda', 'Nissan', 'BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen',
            'Ford', 'Chevrolet', 'Hyundai', 'Kia', 'Mazda', 'Lexus', 'Infiniti',
            'Mitsubishi', 'Subaru', 'Jeep', 'Cadillac', 'Lincoln', 'Acura',
        ]);

        $arabicNames = [
            'Toyota' => 'تويوتا',
            'Honda' => 'هوندا',
            'Nissan' => 'نيسان',
            'BMW' => 'بي ام دبليو',
            'Mercedes-Benz' => 'مرسيدس بنز',
            'Audi' => 'أودي',
            'Volkswagen' => 'فولكس واجن',
            'Ford' => 'فورد',
            'Chevrolet' => 'شيفروليه',
            'Hyundai' => 'هيونداي',
            'Kia' => 'كيا',
            'Mazda' => 'مازda',
            'Lexus' => 'لكزس',
            'Infiniti' => 'إنفينيتي',
            'Mitsubishi' => 'ميتسوبيشي',
            'Subaru' => 'سوبارو',
            'Jeep' => 'جيب',
            'Cadillac' => 'كاديلاك',
            'Lincoln' => 'لنكولن',
            'Acura' => 'أكورا',
        ];

        $countries = [
            'Toyota' => 'JP', 'Honda' => 'JP', 'Nissan' => 'JP', 'Mazda' => 'JP',
            'Mitsubishi' => 'JP', 'Subaru' => 'JP', 'Lexus' => 'JP', 'Infiniti' => 'JP',
            'Acura' => 'JP', 'BMW' => 'DE', 'Mercedes-Benz' => 'DE', 'Audi' => 'DE',
            'Volkswagen' => 'DE', 'Ford' => 'US', 'Chevrolet' => 'US', 'Cadillac' => 'US',
            'Lincoln' => 'US', 'Jeep' => 'US', 'Hyundai' => 'KR', 'Kia' => 'KR',
        ];

        return [
            'name' => $name,
            'name_ar' => $arabicNames[$name] ?? fake()->optional(0.6)->company(),
            'slug' => Str::slug($name),
            'logo' => fake()->optional(0.7)->imageUrl(200, 100, 'transport'),
            'country_origin' => $countries[$name] ?? fake()->countryCode(),
            'is_active' => fake()->boolean(95), // 95% chance of being active
            'added_by_id' => null,
            'deleted_at' => null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the make should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the make should be a luxury brand.
     */
    public function luxury(): static
    {
        $luxuryBrands = ['BMW', 'Mercedes-Benz', 'Audi', 'Lexus', 'Infiniti', 'Cadillac', 'Lincoln', 'Acura'];
        $brand = fake()->randomElement($luxuryBrands);

        return $this->state(fn (array $attributes) => [
            'name' => $brand,
            'slug' => Str::slug($brand),
            'sort_order' => fake()->numberBetween(0, 20), // Higher priority
        ]);
    }

    /**
     * Indicate that the make should be a popular/mainstream brand.
     */
    public function popular(): static
    {
        $popularBrands = ['Toyota', 'Honda', 'Nissan', 'Ford', 'Chevrolet', 'Hyundai', 'Kia'];
        $brand = fake()->randomElement($popularBrands);

        return $this->state(fn (array $attributes) => [
            'name' => $brand,
            'slug' => Str::slug($brand),
            'sort_order' => fake()->numberBetween(0, 10), // Highest priority
        ]);
    }
}
