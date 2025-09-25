<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in dependency order
        $this->call([
            // 1. First seed users (base dependency)
            UserSeeder::class,

            // 2. Then supporting models (vehicle data & subscription plans)
            SupportingModelsSeeder::class,

            // 3. Then vendors (depends on users)
            VendorSeeder::class,

            // 4. Then vehicles (depends on vendors and vehicle data)
            VehicleSeeder::class,

            // 5. Then transactions (depends on users, vendors, vehicles)
            TransactionSeeder::class,

            // 6. Finally additional models (reviews, FAQs, subscriptions)
            // AdditionalModelsSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('Users: '.\App\Models\User::count());
        $this->command->info('Vendors: '.\App\Models\Vendor\Vendor::count());
        $this->command->info('Vehicles: '.\App\Models\Vehicle\Vehicle::count());
        $this->command->info('Transactions: '.\App\Models\Transaction\Transaction::count());
        $this->command->info('Reviews: '.\App\Models\Communication\Review::count());
        $this->command->info('Messages: '.\App\Models\Communication\Message::count());
        $this->command->info('FAQs: '.\App\Models\Content\FAQ::count());
        $this->command->info('Banners: '.\App\Models\Marketing\Banner::count());
        $this->command->info('Payments: '.\App\Models\Transaction\Payment::count());
        $this->command->info('Vehicle Makes: '.\App\Models\Vehicle\VehicleMake::count());
        $this->command->info('Vehicle Models: '.\App\Models\Vehicle\VehicleModel::count());
        $this->command->info('Vehicle Trims: '.\App\Models\Vehicle\VehicleTrim::count());
        $this->command->info('Vehicle Features: '.\App\Models\Vehicle\VehicleFeature::count());
        $this->command->info('Subscription Plans: '.\App\Models\Vendor\SubscriptionPlan::count());
        $this->command->info('Vendor Subscriptions: '.\App\Models\Vendor\VendorSubscription::count());
        $this->command->info('');
        $this->command->info('🔐 Test Accounts:');
        $this->command->info('Super Admin: superadmin@qvex.com / password');
        $this->command->info('Admin: admin@qvex.com / password');
        $this->command->info('Employee: employee@qvex.com / password');
        $this->command->info('Vendor: vendor@qvex.com / password');
        $this->command->info('Vendor Staff: vendorstaff@qvex.com / password');
        $this->command->info('Customer: customer@qvex.com / password');
    }
}
