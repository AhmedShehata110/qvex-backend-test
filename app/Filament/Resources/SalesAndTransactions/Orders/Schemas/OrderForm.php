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
                    ->description(__('keys.basic_order_details_and_customer_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('keys.customer'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('keys.customer_name'))
                                            ->required(),
                                        TextInput::make('email')
                                            ->label(__('keys.email'))
                                            ->email()
                                            ->required(),
                                        TextInput::make('phone')
                                            ->label(__('keys.phone'))
                                            ->tel(),
                                    ]),

                                TextInput::make('order_number')
                                    ->label(__('keys.order_number'))
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->helperText('Auto-generated unique identifier'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_amount')
                                    ->label(__('keys.total_amount'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),

                                Select::make('currency')
                                    ->label(__('keys.currency'))
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
                                    ->label(__('keys.order_date'))
                                    ->default(now())
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label(__('keys.status'))
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
                                    ->label(__('keys.status'))
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
                    ->description(__('keys.shipping_and_delivery_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValue::make('shipping_address')
                                    ->label(__('keys.shipping_address'))
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
                                    ->label(__('keys.billing_address'))
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
                                    ->label(__('keys.shipped_at'))
                                    ->placeholder(__('keys.not_shipped_yet')),

                                DateTimePicker::make('delivered_at')
                                    ->label(__('keys.delivered_at'))
                                    ->placeholder(__('keys.not_delivered_yet')),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->description(__('keys.metadata'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('keys.order_notes'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('keys.order_notes')),

                        KeyValue::make('metadata')
                            ->label(__('keys.metadata'))
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->columnSpanFull()
                            ->helperText('Additional order metadata (optional)'),
                    ]),
            ]);
    }
}
