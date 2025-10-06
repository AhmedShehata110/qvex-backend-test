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
                    ->description(__('keys.core_plan_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('keys.plan_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Display name for the subscription plan'),

                                TextInput::make('slug')
                                    ->label(__('keys.slug'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('URL-friendly identifier'),
                            ]),

                        Textarea::make('description')
                            ->label(__('keys.description'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Detailed description of the plan'),

                        Grid::make(2)
                            ->schema([
                                Select::make('billing_cycle')
                                    ->label(__('keys.billing_cycle'))
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'yearly' => 'Yearly',
                                        'lifetime' => 'Lifetime',
                                    ])
                                    ->default('monthly')
                                    ->required()
                                    ->helperText('How often customers are billed'),

                                TextInput::make('trial_days')
                                    ->label(__('keys.trial_period'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Number of free trial days'),
                            ]),
                    ]),

                Section::make('Pricing')
                    ->description(__('keys.plan_pricing_and_fees'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price_monthly')
                                    ->label(__('keys.price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->required()
                                    ->helperText('Monthly subscription price'),

                                TextInput::make('price_yearly')
                                    ->label(__('keys.price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Annual subscription price'),

                                TextInput::make('setup_fee')
                                    ->label(__('keys.setup_fee'))
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
                                    ->label(__('keys.commission'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->default(0)
                                    ->helperText('Commission percentage for transactions'),
                            ]),
                    ]),

                Section::make('Limits & Quotas')
                    ->description(__('keys.usage_limits_for_the_plan'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('vehicle_listing_limit')
                                    ->label(__('keys.vehicle_listing_limit'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum number of vehicle listings (0 = unlimited)'),

                                TextInput::make('photo_limit_per_vehicle')
                                    ->label(__('keys.photos_per_vehicle'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(10)
                                    ->helperText('Maximum photos allowed per vehicle'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('featured_listing_limit')
                                    ->label(__('keys.featured_listings'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum featured listings allowed'),

                                TextInput::make('staff_account_limit')
                                    ->label(__('keys.staff_accounts'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Maximum staff accounts allowed'),
                            ]),
                    ]),

                Section::make('Features & Capabilities')
                    ->description(__('keys.plan_features_and_capabilities'))
                    ->schema([
                        CheckboxList::make('features')
                            ->label(__('keys.available_features'))
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
                                    ->label(__('keys.analytics_access'))
                                    ->default(false)
                                    ->helperText('Access to detailed analytics'),

                                Toggle::make('priority_support')
                                    ->label(__('keys.support'))
                                    ->default(false)
                                    ->helperText('Priority customer support'),

                                Toggle::make('custom_branding')
                                    ->label(__('keys.custom_branding'))
                                    ->default(false)
                                    ->helperText('Custom branding options'),

                                Toggle::make('api_access')
                                    ->label(__('keys.api_access'))
                                    ->default(false)
                                    ->helperText('Access to API endpoints'),
                            ]),
                    ]),

                Section::make('Plan Settings')
                    ->description(__('keys.plan_visibility_and_ordering'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('keys.active'))
                                    ->default(true)
                                    ->helperText('Plan is available for purchase'),

                                Toggle::make('is_popular')
                                    ->label(__('keys.popular_plan'))
                                    ->default(false)
                                    ->helperText('Highlight as a popular choice'),

                                TextInput::make('sort_order')
                                    ->label(__('keys.sort_order'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Display order (lower numbers appear first)'),
                            ]),
                    ]),
            ]);
    }
}
