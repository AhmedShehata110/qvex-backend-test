<?php

namespace App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Core plan details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Plan Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Display name for the subscription plan'),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('URL-friendly identifier'),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Detailed description of the plan'),

                        Grid::make(2)
                            ->schema([
                                Select::make('billing_cycle')
                                    ->label('Billing Cycle')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'yearly' => 'Yearly',
                                        'lifetime' => 'Lifetime',
                                    ])
                                    ->default('monthly')
                                    ->required()
                                    ->helperText('How often customers are billed'),

                                TextInput::make('trial_days')
                                    ->label('Trial Period (Days)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Number of free trial days'),
                            ]),
                    ]),

                Section::make('Pricing')
                    ->description('Plan pricing and fees')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price_monthly')
                                    ->label('Monthly Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->required()
                                    ->helperText('Monthly subscription price'),

                                TextInput::make('price_yearly')
                                    ->label('Yearly Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Annual subscription price'),

                                TextInput::make('setup_fee')
                                    ->label('Setup Fee')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0)
                                    ->helperText('One-time setup fee'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('commission_rate')
                                    ->label('Commission Rate (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->default(0)
                                    ->helperText('Commission percentage for transactions'),
                            ]),
                    ]),

                Section::make('Limits & Quotas')
                    ->description('Usage limits for the plan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('vehicle_listing_limit')
                                    ->label('Vehicle Listing Limit')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum number of vehicle listings (0 = unlimited)'),

                                TextInput::make('photo_limit_per_vehicle')
                                    ->label('Photos per Vehicle')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(10)
                                    ->helperText('Maximum photos allowed per vehicle'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('featured_listing_limit')
                                    ->label('Featured Listings')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum featured listings allowed'),

                                TextInput::make('staff_account_limit')
                                    ->label('Staff Accounts')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum staff accounts allowed'),
                            ]),
                    ]),

                Section::make('Features & Capabilities')
                    ->description('Plan features and capabilities')
                    ->schema([
                        CheckboxList::make('features')
                            ->label('Available Features')
                            ->options([
                                'unlimited_listings' => 'Unlimited Vehicle Listings',
                                'premium_support' => '24/7 Premium Support',
                                'advanced_analytics' => 'Advanced Analytics & Reports',
                                'custom_domain' => 'Custom Domain',
                                'white_label' => 'White Label Solution',
                                'api_access' => 'API Access',
                                'bulk_upload' => 'Bulk Vehicle Upload',
                                'auto_posting' => 'Auto Posting to Social Media',
                                'lead_management' => 'Advanced Lead Management',
                                'inventory_sync' => 'Inventory Synchronization',
                            ])
                            ->columns(2)
                            ->helperText('Select all features included in this plan'),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('analytics_access')
                                    ->label('Analytics Access')
                                    ->default(false)
                                    ->helperText('Access to detailed analytics'),

                                Toggle::make('priority_support')
                                    ->label('Priority Support')
                                    ->default(false)
                                    ->helperText('Priority customer support'),

                                Toggle::make('custom_branding')
                                    ->label('Custom Branding')
                                    ->default(false)
                                    ->helperText('Custom branding options'),

                                Toggle::make('api_access')
                                    ->label('API Access')
                                    ->default(false)
                                    ->helperText('Access to API endpoints'),
                            ]),
                    ]),

                Section::make('Plan Settings')
                    ->description('Plan visibility and ordering')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Plan is available for purchase'),

                                Toggle::make('is_popular')
                                    ->label('Popular Plan')
                                    ->default(false)
                                    ->helperText('Highlight as a popular choice'),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Display order (lower numbers appear first)'),
                            ]),
                    ]),
            ]);
    }
}
