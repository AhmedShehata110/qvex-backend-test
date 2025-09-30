<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorSubscriptionInfolist
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
                                TextEntry::make('vendor.business_name')
                                    ->label('Vendor')
                                    ->placeholder('No vendor assigned'),

                                TextEntry::make('subscriptionPlan.name')
                                    ->label('Subscription Plan')
                                    ->placeholder('No plan assigned'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'trial' => 'info',
                                        'suspended' => 'danger',
                                        'cancelled' => 'gray',
                                        'expired' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                                TextEntry::make('billing_cycle')
                                    ->label('Billing Cycle')
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('starts_at')
                                    ->label('Start Date')
                                    ->date()
                                    ->placeholder('Not set'),

                                TextEntry::make('ends_at')
                                    ->label('End Date')
                                    ->date()
                                    ->placeholder('Not set'),

                                TextEntry::make('next_billing_date')
                                    ->label('Next Billing Date')
                                    ->date()
                                    ->placeholder('Not set'),
                            ]),
                    ]),

                Section::make('Payment Information')
                    ->description('Payment details and amounts')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('total_amount')
                                    ->label('Total Amount')
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('currency')
                                    ->label('Currency')
                                    ->placeholder('USD'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('tax_amount')
                                    ->label('Tax Amount')
                                    ->money('USD')
                                    ->placeholder('$0.00'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label('Payment Method')
                                    ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : 'Not specified')
                                    ->placeholder('Not specified'),

                                TextEntry::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->placeholder('No reference'),
                            ]),
                    ]),

                Section::make('Usage & Settings')
                    ->description('Subscription usage and configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('listings_used')
                                    ->label('Listings Used')
                                    ->numeric()
                                    ->placeholder('0'),

                                TextEntry::make('featured_listings_used')
                                    ->label('Featured Listings Used')
                                    ->numeric()
                                    ->placeholder('0'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('auto_renewal')
                                    ->label('Auto Renewal')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                                TextEntry::make('cancelled_at')
                                    ->label('Cancelled At')
                                    ->date()
                                    ->placeholder('Not cancelled'),
                            ]),

                        KeyValueEntry::make('metadata')
                            ->label('Additional Metadata')
                            ->columnSpanFull()
                            ->placeholder('No additional metadata'),
                    ]),
            ]);
    }
}
