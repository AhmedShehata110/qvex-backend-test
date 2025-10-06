<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Filament\FilamentServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we're using the testing environment
        $this->app['config']->set('app.env', 'testing');

        // Run migrations for each test
        $this->artisan('migrate:fresh', [
            '--seed' => false,
        ]);

        // Create storage directories for testing
        $this->createStorageDirectories();
    }

    protected function tearDown(): void
    {
        // Clean up any test files
        $this->cleanupTestFiles();

        parent::tearDown();
    }

    /**
     * Create necessary storage directories for testing
     */
    protected function createStorageDirectories(): void
    {
        $directories = [
            storage_path('app/public'),
            storage_path('app/temp'),
            storage_path('logs'),
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }

    /**
     * Clean up test files
     */
    protected function cleanupTestFiles(): void
    {
        // Clean up uploaded test files
        $testFiles = [
            storage_path('app/public/test_*'),
            storage_path('app/temp/*'),
        ];

        foreach ($testFiles as $pattern) {
            $files = glob($pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Create and authenticate a user for testing
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create and authenticate an admin user for Filament testing
     */
    protected function createAdminUser(array $attributes = []): \App\Models\User
    {
        $user = $this->createUser($attributes);
        $user->assignRole('super-admin');
        return $user;
    }

    /**
     * Acting as an authenticated user
     */
    protected function actingAsUser(?\App\Models\User $user = null): static
    {
        $user = $user ?? $this->createUser();
        return $this->actingAs($user);
    }

    /**
     * Acting as an admin user for Filament
     */
    protected function actingAsAdmin(?\App\Models\User $user = null): static
    {
        $user = $user ?? $this->createAdminUser();
        return $this->actingAs($user);
    }
}
