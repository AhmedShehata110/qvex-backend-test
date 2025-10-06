<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Filament Language Switch
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']) // Arabic and English locales
                ->labels([
                    'ar' => 'العربية', // Arabic label
                    'en' => 'English', // English label
                ])
                ->visible(outsidePanels: false) // Only show inside Filament panels
                ->displayLocale('en'); // Display labels in English
        });
    }
}
