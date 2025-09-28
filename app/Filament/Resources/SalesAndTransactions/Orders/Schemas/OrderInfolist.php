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
                                    ->label('Order Number')
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('user.name')
                                    ->label('Customer')
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('total_amount')
                                    ->label('Total Amount')
                                    ->money('USD')
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('status')
                                    ->label('Order Status')
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
                                    ->label('Payment Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'gray',
                                    }),

                                TextEntry::make('order_date')
                                    ->label('Order Date')
                                    ->dateTime(),
                            ]),

                        Section::make('Shipping & Delivery')
                            ->schema([
                                TextEntry::make('shipping_address')
                                    ->label('Shipping Address')
                                    ->state(fn ($record) => is_array($record->shipping_address)
                                        ? implode(', ', array_filter($record->shipping_address))
                                        : $record->shipping_address)
                                    ->placeholder('No shipping address'),

                                TextEntry::make('billing_address')
                                    ->label('Billing Address')
                                    ->state(fn ($record) => is_array($record->billing_address)
                                        ? implode(', ', array_filter($record->billing_address))
                                        : $record->billing_address)
                                    ->placeholder('No billing address'),

                                TextEntry::make('shipped_at')
                                    ->label('Shipped At')
                                    ->dateTime()
                                    ->placeholder('Not shipped'),

                                TextEntry::make('delivered_at')
                                    ->label('Delivered At')
                                    ->dateTime()
                                    ->placeholder('Not delivered'),

                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull()
                                    ->placeholder('No notes'),
                            ]),
                    ]),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label('Days Since Order')
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