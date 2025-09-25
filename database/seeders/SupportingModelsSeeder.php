<?php

namespace Database\Seeders;

use App\Models\Vehicle\VehicleMake;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use App\Models\Vendor\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportingModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->seedSubscriptionPlans(); // Temporarily commented out due to schema mismatch
        $this->seedVehicleData();
    }

    /**
     * Seed subscription plans.
     */
    private function seedSubscriptionPlans(): void
    {
        // Create predefined subscription plans
        SubscriptionPlan::factory()->free()->create();
        SubscriptionPlan::factory()->basic()->monthly()->active()->create();
        SubscriptionPlan::factory()->standard()->monthly()->popular()->active()->create();
        SubscriptionPlan::factory()->premium()->monthly()->active()->create();
        SubscriptionPlan::factory()->enterprise()->monthly()->active()->create();

        // Create yearly versions
        SubscriptionPlan::factory()->basic()->yearly()->active()->create();
        SubscriptionPlan::factory()->standard()->yearly()->active()->create();
        SubscriptionPlan::factory()->premium()->yearly()->active()->create();
        SubscriptionPlan::factory()->enterprise()->yearly()->active()->create();

        $this->command->info('Created subscription plans successfully!');
    }

    /**
     * Seed vehicle makes, models, and trims.
     */
    private function seedVehicleData(): void
    {
        // Clear existing data to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        VehicleTrim::truncate();
        VehicleModel::truncate();
        VehicleMake::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Cleared existing vehicle data.');

        // Create popular vehicle makes
        $popularMakes = [
            'Toyota', 'Honda', 'Nissan', 'Ford', 'Chevrolet',
            'Hyundai', 'Kia', 'Mazda', 'Volkswagen',
        ];

        // Define specific models for each make to avoid duplicates
        $makeModels = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Prius'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Fit'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Maxima'],
            'Ford' => ['F-150', 'Explorer', 'Escape', 'Focus', 'Mustang'],
            'Chevrolet' => ['Silverado', 'Malibu', 'Equinox', 'Tahoe', 'Cruze'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Genesis'],
            'Kia' => ['Forte', 'Optima', 'Sorento', 'Sportage', 'Soul'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5'],
            'Volkswagen' => ['Jetta', 'Passat', 'Golf', 'Tiguan', 'Atlas'],
        ];

        foreach ($popularMakes as $makeName) {
            $make = VehicleMake::factory()->popular()->active()->create([
                'name' => $makeName,
                'slug' => \Illuminate\Support\Str::slug($makeName),
            ]);

            // Create specific models for this make
            $models = $makeModels[$makeName] ?? [];
            foreach ($models as $modelName) {
                $model = VehicleModel::factory()
                    ->active()
                    ->currentProduction()
                    ->create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => \Illuminate\Support\Str::slug($modelName),
                    ]);

                // Create 2-4 trims for each model
                $trimCount = fake()->numberBetween(2, 4);
                VehicleTrim::factory()
                    ->count($trimCount)
                    ->active()
                    ->create(['model_id' => $model->id]);
            }
        }

        // Create luxury vehicle makes
        $luxuryMakes = ['BMW', 'Mercedes-Benz', 'Audi', 'Lexus', 'Infiniti', 'Cadillac'];
        $luxuryModels = [
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'GLC', 'GLE'],
            'Audi' => ['A4', 'A6', 'Q5', 'Q7'],
            'Lexus' => ['ES', 'RX', 'NX', 'GX'],
            'Infiniti' => ['Q50', 'QX50', 'QX60', 'QX80'],
            'Cadillac' => ['ATS', 'CTS', 'XT5', 'Escalade'],
        ];

        foreach ($luxuryMakes as $makeName) {
            $make = VehicleMake::factory()->luxury()->active()->create([
                'name' => $makeName,
                'slug' => \Illuminate\Support\Str::slug($makeName),
            ]);

            // Create specific models for this luxury make
            $models = $luxuryModels[$makeName] ?? [];
            foreach ($models as $modelName) {
                $model = VehicleModel::factory()
                    ->active()
                    ->currentProduction()
                    ->create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => \Illuminate\Support\Str::slug($modelName),
                    ]);

                // Create 2-3 trims for each luxury model
                $trimCount = fake()->numberBetween(2, 3);
                VehicleTrim::factory()
                    ->count($trimCount)
                    ->active()
                    ->luxury()
                    ->create(['model_id' => $model->id]);

                // Add a performance trim occasionally
                if (fake()->boolean(30)) {
                    VehicleTrim::factory()
                        ->active()
                        ->performance()
                        ->create(['model_id' => $model->id]);
                }
            }
        }

        // Additional makes section removed to avoid conflicts

        $this->command->info('Created vehicle data successfully!');
        $this->command->info('Makes: '.VehicleMake::count());
        $this->command->info('Models: '.VehicleModel::count());
        $this->command->info('Trims: '.VehicleTrim::count());
    }
}
