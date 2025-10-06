<?php

namespace Database\Seeders;

use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\Brand;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class SimpleVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some vendors for relationships
        $activeVendors = Vendor::where('status', 'active')
            ->where('is_active', true)
            ->limit(10)
            ->get();

        if ($activeVendors->isEmpty()) {
            $this->command->warn('No active vendors found. Cannot create vehicles.');

            return;
        }

        // Get vehicle data for realistic relationships
        $makes = Brand::where('is_active', true)->get();
        $models = VehicleModel::where('is_active', true)->get();
        $trims = VehicleTrim::where('is_active', true)->get();

        if ($makes->isEmpty() || $models->isEmpty() || $trims->isEmpty()) {
            $this->command->warn('Vehicle makes/models/trims not found. Run SupportingModelsSeeder first.');

            return;
        }

        $this->command->info('Creating vehicles with simplified approach...');

        // Create new vehicles for sale (5)
        for ($i = 0; $i < 5; $i++) {
            $vendor = $activeVendors->random();
            $make = $makes->random();
            $makeModels = $models->where('brand_id', $make->id);

            if ($makeModels->isEmpty()) {
                $model = $models->random();
            } else {
                $model = $makeModels->random();
            }

            $modelTrims = $trims->where('model_id', $model->id);

            if ($modelTrims->isEmpty()) {
                $trim = $trims->random();
            } else {
                $trim = $modelTrims->random();
            }

            Vehicle::factory()
                ->newCondition()
                ->forSale()
                ->activeAndApproved()
                ->create([
                    'vendor_id' => $vendor->id,
                    'brand_id' => $make->id,
                    'model_id' => $model->id,
                    'trim_id' => $trim->id,
                ]);
        }

        // Create used vehicles (10)
        for ($i = 0; $i < 10; $i++) {
            $vendor = $activeVendors->random();
            $make = $makes->random();
            $makeModels = $models->where('brand_id', $make->id);

            if ($makeModels->isEmpty()) {
                $model = $models->random();
            } else {
                $model = $makeModels->random();
            }

            $modelTrims = $trims->where('model_id', $model->id);

            if ($modelTrims->isEmpty()) {
                $trim = $trims->random();
            } else {
                $trim = $modelTrims->random();
            }

            Vehicle::factory()
                ->used()
                ->forSale()
                ->activeAndApproved()
                ->create([
                    'vendor_id' => $vendor->id,
                    'brand_id' => $make->id,
                    'model_id' => $model->id,
                    'trim_id' => $trim->id,
                ]);
        }

        // Create rental vehicles (5)
        for ($i = 0; $i < 5; $i++) {
            $vendor = $activeVendors->random();
            $make = $makes->random();
            $makeModels = $models->where('brand_id', $make->id);

            if ($makeModels->isEmpty()) {
                $model = $models->random();
            } else {
                $model = $makeModels->random();
            }

            $modelTrims = $trims->where('model_id', $model->id);

            if ($modelTrims->isEmpty()) {
                $trim = $trims->random();
            } else {
                $trim = $modelTrims->random();
            }

            Vehicle::factory()
                ->forRent()
                ->activeAndApproved()
                ->create([
                    'vendor_id' => $vendor->id,
                    'brand_id' => $make->id,
                    'model_id' => $model->id,
                    'trim_id' => $trim->id,
                ]);
        }

        $this->command->info('Created vehicles successfully!');
        $this->command->info('Total vehicles: '.Vehicle::count());
        $this->command->info('Active vehicles: '.Vehicle::where('status', 'active')->count());
    }
}
