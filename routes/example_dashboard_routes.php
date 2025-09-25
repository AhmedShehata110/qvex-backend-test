<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Admin Only)
|--------------------------------------------------------------------------
|
| These routes are protected by the DashboardAccess middleware.
| Only users with user_type = 'admin' can access these routes.
|
*/

Route::middleware(['auth', 'dashboard.access'])->prefix('dashboard')->group(function () {

    // Main dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin management routes
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    });

    // Other dashboard sections...
    // All routes here will require admin user_type
});

/*
|--------------------------------------------------------------------------
| Public Routes (All Users)
|--------------------------------------------------------------------------
|
| These routes are accessible to both admin and regular users.
|
*/

Route::middleware(['auth'])->group(function () {

    // User profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Vehicle browsing (available to all authenticated users)
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');

    // Other public authenticated routes...
});

/*
|--------------------------------------------------------------------------
| Registration in bootstrap/app.php or app/Http/Kernel.php
|--------------------------------------------------------------------------
|
| To use this middleware, register it in your application:
|
| // In bootstrap/app.php (Laravel 11+)
| ->withMiddleware(function (Middleware $middleware) {
|     $middleware->alias([
|         'dashboard.access' => \App\Http\Middleware\DashboardAccess::class,
|     ]);
| })
|
| // Or in app/Http/Kernel.php (Laravel 10 and below)
| protected $middlewareAliases = [
|     'dashboard.access' => \App\Http\Middleware\DashboardAccess::class,
| ];
|
*/
