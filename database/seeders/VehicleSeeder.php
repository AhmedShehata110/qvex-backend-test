<?php

namespace Database\Seeders;

use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleMake;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some vendors for relationships
        $activeVendors = Vendor::where('status', 'active')
            ->where('is_active', true)
            ->limit(20)
            ->get();

        if ($activeVendors->isEmpty()) {
            $this->command->warn('No active vendors found. Creating some vehicles without vendor relationships.');
        }

        // Get vehicle data for realistic relationships
        $makes = VehicleMake::where('is_active', true)->get();
        $models = VehicleModel::where('is_active', true)->get();
        $trims = VehicleTrim::where('is_active', true)->get();

        if ($makes->isEmpty() || $models->isEmpty() || $trims->isEmpty()) {
            $this->command->warn('Vehicle makes/models/trims not found. Run SupportingModelsSeeder first.');

            return;
        }

        // Featured new vehicles for sale (10)
        Vehicle::factory()
            ->count(10)
            ->newCondition()
            ->forSale()
            ->activeAndApproved()
            ->featured()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Regular new vehicles for sale (15)
        Vehicle::factory()
            ->count(15)
            ->newCondition()
            ->forSale()
            ->activeAndApproved()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Used vehicles for sale (40)
        Vehicle::factory()
            ->count(40)
            ->used()
            ->forSale()
            ->activeAndApproved()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Popular used vehicles (15)
        Vehicle::factory()
            ->count(15)
            ->used()
            ->forSale()
            ->activeAndApproved()
            ->popular()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Luxury vehicles for sale (8)
        Vehicle::factory()
            ->count(8)
            ->luxury()
            ->forSale()
            ->activeAndApproved()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims, true));

        // Rental vehicles (20)
        Vehicle::factory()
            ->count(20)
            ->forRent()
            ->activeAndApproved()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Featured rental vehicles (5)
        Vehicle::factory()
            ->count(5)
            ->forRent()
            ->activeAndApproved()
            ->featured()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Vehicles available for both sale and rent (12)
        Vehicle::factory()
            ->count(12)
            ->state(['availability_type' => 'both'])
            ->activeAndApproved()
            ->create(array_merge(
                $this->getVehicleAttributes($activeVendors, $makes, $models, $trims),
                [
                    'rental_daily_rate' => fake()->randomFloat(2, 80, 300),
                    'rental_weekly_rate' => fake()->randomFloat(2, 500, 2000),
                    'rental_monthly_rate' => fake()->randomFloat(2, 2000, 8000),
                    'security_deposit' => fake()->randomFloat(2, 1000, 3000),
                ]
            ));

        // Sold vehicles (8)
        Vehicle::factory()
            ->count(8)
            ->sold()
            ->forSale()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Pending approval vehicles (12)
        Vehicle::factory()
            ->count(12)
            ->pendingApproval()
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Draft vehicles (5)
        Vehicle::factory()
            ->count(5)
            ->state(['status' => 'draft'])
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        // Inactive vehicles (3)
        Vehicle::factory()
            ->count(3)
            ->state(['status' => 'inactive', 'is_active' => false])
            ->create($this->getVehicleAttributes($activeVendors, $makes, $models, $trims));

        $this->command->info('Created vehicles successfully!');
        $this->command->info('Total vehicles: '.Vehicle::count());
        $this->command->info('Active vehicles: '.Vehicle::where('status', 'active')->count());
        $this->command->info('Featured vehicles: '.Vehicle::where('is_featured', true)->count());
        $this->command->info('For sale: '.Vehicle::whereIn('availability_type', ['sale', 'both'])->count());
        $this->command->info('For rent: '.Vehicle::whereIn('availability_type', ['rent', 'both'])->count());
    }

    /**
     * Get vehicle attributes with realistic relationships.
     */
    private function getVehicleAttributes($vendors, $makes, $models, $trims, $isLuxury = false): array
    {
        $attributes = [];

        // Set vendor if available
        if ($vendors->isNotEmpty()) {
            $attributes['vendor_id'] = $vendors->random()->id;
        }

        // Set realistic make/model/trim relationships
        if ($makes->isNotEmpty()) {
            $make = $makes->random();
            $attributes['make_id'] = $make->id;

            // Get models for this make
            $makeModels = $models->where('make_id', $make->id);
            if ($makeModels->isNotEmpty()) {
                $model = $makeModels->random();
                $attributes['model_id'] = $model->id;

                // Get trims for this model
                $modelTrims = $trims->where('model_id', $model->id);
                if ($modelTrims->isNotEmpty()) {
                    $attributes['trim_id'] = $modelTrims->random()->id;
                }
            }
        }

        // For luxury vehicles, prefer luxury makes
        if ($isLuxury) {
            $luxuryMakes = $makes->whereIn('name', ['BMW', 'Mercedes-Benz', 'Audi', 'Lexus', 'Infiniti', 'Cadillac']);
            if ($luxuryMakes->isNotEmpty()) {
                $make = $luxuryMakes->random();
                $attributes['make_id'] = $make->id;

                $makeModels = $models->where('make_id', $make->id);
                if ($makeModels->isNotEmpty()) {
                    $model = $makeModels->random();
                    $attributes['model_id'] = $model->id;

                    $modelTrims = $trims->where('model_id', $model->id);
                    if ($modelTrims->isNotEmpty()) {
                        $attributes['trim_id'] = $modelTrims->random()->id;
                    }
                }
            }
        }

        return $attributes;
    }
}
