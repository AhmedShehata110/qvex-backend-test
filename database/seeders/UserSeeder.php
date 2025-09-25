<?php

namespace Database\Seeders;

use App\Enums\User\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::where('email', 'admin@qvex.com')->first();
        if ($admin) {
            $this->command->info('Admin user already exists, updating...');
            $admin->update([
                'name' => 'System Administrator',
                'phone' => '+1234567899',
                'user_type' => UserTypeEnum::ADMIN,
                'is_active' => true,
            ]);
        } else {
            User::factory()
                ->admin()
                ->create([
                    'email' => 'admin@qvex.com',
                    'name' => 'System Administrator',
                    'phone' => '+1234567899',
                ]);
        }

        // Create test users for different types
        $testUsers = [
            [
                'email' => 'testadmin@qvex.com',
                'name' => 'Test Administrator',
                'factory_state' => 'admin',
            ],
            [
                'email' => 'user@qvex.com',
                'name' => 'Test User',
                'factory_state' => 'user',
            ],
            [
                'email' => 'vendor@qvex.com',
                'name' => 'Test Vendor',
                'factory_state' => 'vendor',
            ],
        ];

        foreach ($testUsers as $userData) {
            $factoryState = $userData['factory_state'];
            unset($userData['factory_state']);

            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $this->command->info('User '.$userData['email'].' already exists, skipping...');

                continue;
            }

            User::factory()
                ->$factoryState()
                ->create($userData);
        }

        // Create additional admin users
        User::factory()
            ->count(5)
            ->admin()
            ->create();

        // Create regular users
        User::factory()
            ->count(30)
            ->user()
            ->create();

        // Create some Arabic users
        User::factory()
            ->count(10)
            ->arabic()
            ->user()
            ->create();

        // Create some users with two-factor authentication
        User::factory()
            ->count(5)
            ->withTwoFactor()
            ->recentlyActive()
            ->create();

        // Create some unverified users
        User::factory()
            ->count(8)
            ->unverified()
            ->create();

        // Create some inactive users
        User::factory()
            ->count(3)
            ->inactive()
            ->create();

        $this->command->info('Created users successfully!');
        $this->command->info('Admin: admin@qvex.com / password');
        $this->command->info('Test Admin: testadmin@qvex.com / password');
        $this->command->info('Test User: user@qvex.com / password');
    }
}
