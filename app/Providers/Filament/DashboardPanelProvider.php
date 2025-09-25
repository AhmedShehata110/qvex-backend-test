<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->brandName('Qvex')
            ->colors([
                'primary' => [
                    50 => '#f0fdf4',
                    100 => '#dcfce7',
                    200 => '#bbf7d0',
                    300 => '#86efac',
                    400 => '#4ade80',
                    500 => '#2ECC71', // QVEX Emerald Green
                    600 => '#27AE60', // QVEX Forest Green
                    700 => '#15803d',
                    800 => '#166534',
                    900 => '#14532d',
                    950 => '#052e16',
                ],
                'success' => [
                    50 => '#f7fee7',
                    100 => '#ecfccb',
                    200 => '#d9f99d',
                    300 => '#bef264',
                    400 => '#A4D65E', // QVEX Lime Green
                    500 => '#84cc16',
                    600 => '#65a30d',
                    700 => '#4d7c0f',
                    800 => '#3f6212',
                    900 => '#365314',
                    950 => '#1a2e05',
                ],
                'gray' => [
                    50 => '#f8fafc',
                    100 => '#f1f5f9',
                    200 => '#e2e8f0',
                    300 => '#cbd5e1',
                    400 => '#94a3b8',
                    500 => '#7F8C8D', // QVEX Steel Gray
                    600 => '#475569',
                    700 => '#334155',
                    800 => '#2C3E50', // QVEX Dark Navy
                    900 => '#0f172a',
                    950 => '#020617',
                ],
                'info' => Color::Sky,
                'warning' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#FFE7BB', // QVEX Warm Cream (adjusted for better contrast)
                    600 => '#d97706',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'danger' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Dashboard')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Vehicle Management')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Users & Vendors')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Sales & Transactions')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Marketing')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Content')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Communication')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Reviews & Communication')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('E-commerce')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Locations')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Administration')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Utilities')
                    ->collapsible(),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
