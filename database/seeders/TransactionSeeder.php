<?php

namespace Database\Seeders;

use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data for relationships
        $customers = User::whereDoesntHave('vendor')->limit(30)->get();
        $vendors = Vendor::with('user')->where('status', 'active')->limit(20)->get();
        $vehicles = Vehicle::where('status', 'active')->limit(50)->get();

        if ($customers->isEmpty() || $vendors->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('Insufficient data found. Ensure users, vendors, and vehicles exist.');

            return;
        }

        // Completed sale transactions (25)
        Transaction::factory()
            ->count(25)
            ->sale()
            ->completed()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        // Recent pending sale transactions (15)
        Transaction::factory()
            ->count(15)
            ->sale()
            ->pending()
            ->recent()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        // Paid sale transactions (20)
        Transaction::factory()
            ->count(20)
            ->sale()
            ->paid()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        // High-value sale transactions (8)
        Transaction::factory()
            ->count(8)
            ->sale()
            ->highValue()
            ->completed()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        // Completed rental transactions (30)
        Transaction::factory()
            ->count(30)
            ->rental()
            ->completed()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'rent'));

        // Active rental transactions (12)
        Transaction::factory()
            ->count(12)
            ->rental()
            ->paid()
            ->recent()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'rent'));

        // Pending rental transactions (8)
        Transaction::factory()
            ->count(8)
            ->rental()
            ->pending()
            ->recent()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'rent'));

        // Lease transactions (10)
        Transaction::factory()
            ->count(10)
            ->lease()
            ->paid()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        // Cancelled transactions (12)
        Transaction::factory()
            ->count(12)
            ->cancelled()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles));

        // Disputed transactions (3)
        Transaction::factory()
            ->count(3)
            ->disputed()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles));

        // Recent high-value transactions (5)
        Transaction::factory()
            ->count(5)
            ->sale()
            ->highValue()
            ->paid()
            ->recent()
            ->create($this->getTransactionAttributes($customers, $vendors, $vehicles, 'sale'));

        $this->command->info('Created transactions successfully!');
        $this->command->info('Total transactions: '.Transaction::count());
        $this->command->info('Completed transactions: '.Transaction::where('status', 'completed')->count());
        $this->command->info('Sale transactions: '.Transaction::where('type', 'sale')->count());
        $this->command->info('Rental transactions: '.Transaction::where('type', 'rental')->count());
        $this->command->info('Lease transactions: '.Transaction::where('type', 'lease')->count());
    }

    /**
     * Get transaction attributes with realistic relationships.
     */
    private function getTransactionAttributes($customers, $vendors, $vehicles, $vehicleType = null): array
    {
        $attributes = [];

        // Set buyer (customer)
        if ($customers->isNotEmpty()) {
            $attributes['buyer_id'] = $customers->random()->id;
        }

        // Set seller (vendor user) and vehicle
        if ($vendors->isNotEmpty()) {
            $vendor = $vendors->random();
            $attributes['seller_id'] = $vendor->user_id;

            // Try to get a vehicle from this vendor
            $vendorVehicles = $vehicles->where('vendor_id', $vendor->id);
            if ($vendorVehicles->isNotEmpty()) {
                // Filter by vehicle type if specified
                if ($vehicleType) {
                    $filteredVehicles = $vendorVehicles->filter(function ($vehicle) use ($vehicleType) {
                        return in_array($vehicle->availability_type, [$vehicleType, 'both']);
                    });

                    if ($filteredVehicles->isNotEmpty()) {
                        $attributes['vehicle_id'] = $filteredVehicles->random()->id;
                    } else {
                        $attributes['vehicle_id'] = $vendorVehicles->random()->id;
                    }
                } else {
                    $attributes['vehicle_id'] = $vendorVehicles->random()->id;
                }
            } elseif ($vehicles->isNotEmpty()) {
                // Fallback to any vehicle
                $vehicle = $vehicles->random();
                $attributes['vehicle_id'] = $vehicle->id;
                // Update seller to match vehicle's vendor
                if ($vehicle->vendor && $vehicle->vendor->user) {
                    $attributes['seller_id'] = $vehicle->vendor->user->id;
                }
            }
        } elseif ($vehicles->isNotEmpty()) {
            // Fallback to any vehicle and use its vendor as seller
            $vehicle = $vehicles->random();
            $attributes['vehicle_id'] = $vehicle->id;
            if ($vehicle->vendor && $vehicle->vendor->user) {
                $attributes['seller_id'] = $vehicle->vendor->user->id;
            }
        }

        return $attributes;
    }
}
