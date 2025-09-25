<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test vendor for the test vendor user
        $testVendorUser = User::where('email', 'vendor@qvex.com')->first();
        if ($testVendorUser) {
            // Check if the vendor already exists to prevent unique constraint violation
            $testVendor = Vendor::where('slug', 'test-dealership')->first();

            if (! $testVendor) {
                Vendor::factory()
                    ->activeAndVerified()
                    ->dealership()
                    ->create([
                        'user_id' => $testVendorUser->id,
                        'business_name' => 'Test Dealership',
                        'business_name_ar' => 'وكالة الاختبار',
                        'slug' => 'test-dealership',
                        'registration_number' => 'TEST123456',
                        'description' => 'This is a test dealership for development purposes.',
                        'description_ar' => 'هذه وكالة اختبار لأغراض التطوير.',
                    ]);
            } else {
                $this->command->info('Test Dealership vendor already exists, skipping creation.');
            }
        }

        // Create various types of vendors

        // Featured dealerships (5)
        Vendor::factory()
            ->count(5)
            ->dealership()
            ->activeAndVerified()
            ->featured()
            ->highRated()
            ->create();

        // Regular dealerships (8)
        Vendor::factory()
            ->count(8)
            ->dealership()
            ->activeAndVerified()
            ->create();

        // Rental companies (6)
        Vendor::factory()
            ->count(6)
            ->rentalCompany()
            ->activeAndVerified()
            ->create();

        // Featured rental companies (2)
        Vendor::factory()
            ->count(2)
            ->rentalCompany()
            ->activeAndVerified()
            ->featured()
            ->highRated()
            ->create();

        // Individual sellers (12)
        Vendor::factory()
            ->count(12)
            ->individual()
            ->activeAndVerified()
            ->create();

        // Service centers (4)
        Vendor::factory()
            ->count(4)
            ->serviceCenter()
            ->activeAndVerified()
            ->create();

        // Pending vendors (5)
        Vendor::factory()
            ->count(5)
            ->pending()
            ->create();

        // Suspended vendors (2)
        Vendor::factory()
            ->count(2)
            ->suspended()
            ->create();

        // High-rated vendors across different types (3)
        Vendor::factory()
            ->count(2)
            ->dealership()
            ->activeAndVerified()
            ->highRated()
            ->create();

        Vendor::factory()
            ->count(1)
            ->rentalCompany()
            ->activeAndVerified()
            ->highRated()
            ->create();

        $this->command->info('Created vendors successfully!');
        $this->command->info('Test vendor business: Test Dealership (vendor@qvex.com)');
    }
}
