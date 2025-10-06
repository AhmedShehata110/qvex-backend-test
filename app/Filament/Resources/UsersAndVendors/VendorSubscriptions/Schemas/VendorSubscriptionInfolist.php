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
                    ->description(__('keys.basic_subscription_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('vendor.business_name')
                                    ->label(__('keys.vendor'))
                                    ->placeholder(__('keys.no_vendor_assigned')),

                                TextEntry::make('subscriptionPlan.name')
                                    ->label(__('keys.subscription_plan'))
                                    ->placeholder(__('keys.no_plan_assigned')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('keys.status'))
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
                                    ->label(__('keys.billing_cycle'))
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('starts_at')
                                    ->label(__('keys.start_date'))
                                    ->date()
                                    ->placeholder(__('keys.not_set')),

                                TextEntry::make('ends_at')
                                    ->label(__('keys.end_date'))
                                    ->date()
                                    ->placeholder(__('keys.not_set')),

                                TextEntry::make('next_billing_date')
                                    ->label(__('keys.next_billing_date'))
                                    ->date()
                                    ->placeholder(__('keys.not_set')),
                            ]),
                    ]),
 
                Section::make('Payment Information')
                    ->description(__('keys.payment_details_and_amounts'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('amount_paid')
                                    ->label(__('keys.amount_paid'))
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('total_amount')
                                    ->label(__('keys.paid_amount'))
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('currency')
                                    ->label(__('keys.currency'))
                                    ->placeholder(__('keys.usd')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('discount_amount')
                                    ->label(__('keys.discount_amount'))
                                    ->money('USD')
                                    ->placeholder('$0.00'),

                                TextEntry::make('tax_amount')
                                    ->label(__('keys.tax_amount'))
                                    ->money('USD')
                                    ->placeholder('$0.00'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label(__('keys.payment_method'))
                                    ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : 'Not specified')
                                    ->placeholder(__('keys.not_specified')),

                                TextEntry::make('payment_reference')
                                    ->label(__('keys.payment_reference'))
                                    ->placeholder(__('keys.no_reference')),
                            ]),
                    ]),

                Section::make('Usage & Settings')
                    ->description(__('keys.subscription_usage_and_configuration'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('listings_used')
                                    ->label(__('keys.listings_used'))
                                    ->numeric()
                                    ->placeholder(__('keys.zero')),

                                TextEntry::make('featured_listings_used')
                                    ->label(__('keys.featured_listings_used'))
                                    ->numeric()
                                    ->placeholder(__('keys.zero')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('auto_renewal')
                                    ->label(__('keys.auto_renewal'))
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                                TextEntry::make('cancelled_at')
                                    ->label(__('keys.cancelled_at'))
                                    ->date()
                                    ->placeholder('Not cancelled'),
                            ]),

                        KeyValueEntry::make('metadata')
                            ->label(__('keys.metadata'))
                            ->columnSpanFull()
                            ->placeholder('No additional metadata'),
                    ]),
            ]);
    }
}
