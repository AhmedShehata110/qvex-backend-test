<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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


            // Set global default image for empty media columns/entries
            SpatieMediaLibraryImageColumn::configureUsing(function ($column) {
                $column->defaultImageUrl(asset('defaults/default-image.png'));
            });

            SpatieMediaLibraryImageEntry::configureUsing(function ($entry) {
                $entry->defaultImageUrl(asset('defaults/default-image.png'));
            });


    }
}
