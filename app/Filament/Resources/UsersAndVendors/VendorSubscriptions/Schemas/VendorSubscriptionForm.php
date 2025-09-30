<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Details')
                    ->description('Basic subscription information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'business_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Select the vendor for this subscription'),

                                Select::make('subscription_plan_id')
                                    ->label('Subscription Plan')
                                    ->relationship('subscriptionPlan', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Choose the subscription plan'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'pending' => 'Pending',
                                        'trial' => 'Trial',
                                        'suspended' => 'Suspended',
                                        'cancelled' => 'Cancelled',
                                        'expired' => 'Expired',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->helperText('Current subscription status'),

                                Select::make('billing_cycle')
                                    ->label('Billing Cycle')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly',
                                        'semi_annual' => 'Semi-Annual',
                                        'annual' => 'Annual',
                                    ])
                                    ->default('monthly')
                                    ->required()
                                    ->helperText('How often the subscription is billed'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('starts_at')
                                    ->label('Start Date')
                                    ->default(now())
                                    ->required()
                                    ->helperText('When the subscription begins'),

                                DatePicker::make('ends_at')
                                    ->label('End Date')
                                    ->required()
                                    ->helperText('When the subscription expires'),

                                DatePicker::make('next_billing_date')
                                    ->label('Next Billing Date')
                                    ->helperText('When the next payment is due'),
                            ]),
                    ]),

                Section::make('Payment Information')
                    ->description('Payment details and amounts')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->required()
                                    ->helperText('Amount actually paid'),

                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->required()
                                    ->helperText('Total subscription amount'),

                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        'USD' => 'USD',
                                        'EUR' => 'EUR',
                                        'GBP' => 'GBP',
                                        'CAD' => 'CAD',
                                        'AUD' => 'AUD',
                                    ])
                                    ->default('USD')
                                    ->required()
                                    ->helperText('Payment currency'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Discount applied to the subscription'),

                                TextInput::make('tax_amount')
                                    ->label('Tax Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Tax amount included'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->options([
                                        'credit_card' => 'Credit Card',
                                        'bank_transfer' => 'Bank Transfer',
                                        'paypal' => 'PayPal',
                                        'stripe' => 'Stripe',
                                    ])
                                    ->helperText('How the payment was made'),

                                TextInput::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->maxLength(255)
                                    ->helperText('Transaction ID or reference number'),
                            ]),
                    ]),

                Section::make('Usage & Settings')
                    ->description('Subscription usage and configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('listings_used')
                                    ->label('Listings Used')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Number of listings already used'),

                                TextInput::make('featured_listings_used')
                                    ->label('Featured Listings Used')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Number of featured listings used'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('auto_renewal')
                                    ->label('Auto Renewal')
                                    ->default(true)
                                    ->helperText('Automatically renew the subscription'),

                                DatePicker::make('cancelled_at')
                                    ->label('Cancelled At')
                                    ->helperText('Date when subscription was cancelled'),
                            ]),

                        Textarea::make('metadata')
                            ->label('Additional Metadata')
                            ->rows(3)
                            ->json()
                            ->helperText('Additional subscription data (JSON format)'),
                    ]),
            ]);
    }
}
