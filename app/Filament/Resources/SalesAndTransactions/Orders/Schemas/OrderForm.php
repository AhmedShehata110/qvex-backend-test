<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Schemas;

use App\Models\SalesAndTransactions\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->description('Basic order details and customer information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Customer')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Customer Name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required(),
                                        TextInput::make('phone')
                                            ->label('Phone')
                                            ->tel(),
                                    ]),

                                TextInput::make('order_number')
                                    ->label('Order Number')
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->helperText('Auto-generated unique identifier'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),

                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        'USD' => 'USD',
                                        'EUR' => 'EUR',
                                        'SAR' => 'SAR',
                                        'AED' => 'AED',
                                        'EGP' => 'EGP',
                                    ])
                                    ->default('USD')
                                    ->required(),

                                DateTimePicker::make('order_date')
                                    ->label('Order Date')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Order Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->live(),

                                Select::make('payment_status')
                                    ->label('Payment Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'refunded' => 'Refunded',
                                        'partially_refunded' => 'Partially Refunded',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Shipping & Delivery')
                    ->description('Shipping and delivery information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValue::make('shipping_address')
                                    ->label('Shipping Address')
                                    ->keyLabel('Field')
                                    ->valueLabel('Value')
                                    ->default([
                                        'street' => '',
                                        'city' => '',
                                        'state' => '',
                                        'country' => '',
                                        'postal_code' => '',
                                    ])
                                    ->columnSpanFull(),

                                KeyValue::make('billing_address')
                                    ->label('Billing Address')
                                    ->keyLabel('Field')
                                    ->valueLabel('Value')
                                    ->default([
                                        'street' => '',
                                        'city' => '',
                                        'state' => '',
                                        'country' => '',
                                        'postal_code' => '',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('shipped_at')
                                    ->label('Shipped At')
                                    ->placeholder('Not shipped yet'),

                                DateTimePicker::make('delivered_at')
                                    ->label('Delivered At')
                                    ->placeholder('Not delivered yet'),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->description('Notes and metadata')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Order Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Any additional notes about this order'),

                        KeyValue::make('metadata')
                            ->label('Additional Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->columnSpanFull()
                            ->helperText('Additional order metadata (optional)'),
                    ]),
            ]);
    }
}
