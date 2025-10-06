<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Order Information')
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label(__('keys.order_number'))
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('user.name')
                                    ->label(__('keys.customer'))
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('total_amount')
                                    ->label(__('keys.total_amount'))
                                    ->money('USD')
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('status')
                                    ->label(__('keys.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('payment_status')
                                    ->label(__('keys.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'gray',
                                    }),

                                TextEntry::make('order_date')
                                    ->label(__('keys.order_date'))
                                    ->dateTime(),
                            ]),

                        Section::make('Shipping & Delivery')
                            ->schema([
                                TextEntry::make('shipping_address')
                                    ->label(__('keys.shipping_address'))
                                    ->state(fn ($record) => is_array($record->shipping_address)
                                        ? implode(', ', array_filter($record->shipping_address))
                                        : $record->shipping_address)
                                    ->placeholder(__('keys.no_shipping_address')),

                                TextEntry::make('billing_address')
                                    ->label(__('keys.billing_address'))
                                    ->state(fn ($record) => is_array($record->billing_address)
                                        ? implode(', ', array_filter($record->billing_address))
                                        : $record->billing_address)
                                    ->placeholder(__('keys.no_billing_address')),

                                TextEntry::make('shipped_at')
                                    ->label(__('keys.shipped_at'))
                                    ->dateTime()
                                    ->placeholder(__('keys.not_shipped')),

                                TextEntry::make('delivered_at')
                                    ->label(__('keys.delivered_at'))
                                    ->dateTime()
                                    ->placeholder(__('keys.not_delivered')),

                                TextEntry::make('notes')
                                    ->label(__('keys.notes'))
                                    ->columnSpanFull()
                                    ->placeholder(__('keys.no_notes')),
                            ]),
                    ]),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('keys.created_at'))
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label(__('keys.updated_at'))
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label(__('keys.days_since_order'))
                                    ->getStateUsing(fn ($record) => $record->created_at->diffInDays().' days ago')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
