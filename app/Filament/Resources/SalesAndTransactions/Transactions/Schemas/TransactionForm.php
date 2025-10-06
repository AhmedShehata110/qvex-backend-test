<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Schemas;

use App\Models\Transaction\Transaction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->description(__('keys.basic_transaction_information_and_participants'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('transaction_number')
                                    ->label(__('keys.transaction_number'))
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->helperText('Auto-generated unique identifier'),

                                Select::make('type')
                                    ->label(__('keys.type'))
                                    ->options([
                                        Transaction::TYPE_SALE => 'Sale',
                                        Transaction::TYPE_RENTAL => 'Rental',
                                        Transaction::TYPE_LEASE => 'Lease',
                                    ])
                                    ->required()
                                    ->live(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('buyer_id')
                                    ->label(__('keys.buyer'))
                                    ->relationship('buyer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('seller_id')
                                    ->label(__('keys.seller'))
                                    ->relationship('seller', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('vehicle_id')
                                    ->label(__('keys.vehicle'))
                                    ->relationship('vehicle', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Select::make('status')
                            ->label(__('keys.status'))
                            ->options([
                                Transaction::STATUS_PENDING => 'Pending',
                                Transaction::STATUS_CONFIRMED => 'Confirmed',
                                Transaction::STATUS_PAYMENT_PENDING => 'Payment Pending',
                                Transaction::STATUS_PAID => 'Paid',
                                Transaction::STATUS_IN_PROGRESS => 'In Progress',
                                Transaction::STATUS_COMPLETED => 'Completed',
                                Transaction::STATUS_CANCELLED => 'Cancelled',
                                Transaction::STATUS_REFUNDED => 'Refunded',
                                Transaction::STATUS_DISPUTED => 'Disputed',
                            ])
                            ->required()
                            ->default(Transaction::STATUS_PENDING),
                    ]),

                Section::make('Financial Information')
                    ->description(__('keys.transaction_amounts_and_payment_details'))
                    ->schema([
                        Fieldset::make('Amount Breakdown')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('subtotal')
                                            ->label(__('keys.subtotal'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->step(0.01),

                                        TextInput::make('tax_amount')
                                            ->label(__('keys.tax_amount'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('commission_amount')
                                            ->label(__('keys.commission'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),

                                        TextInput::make('total_amount')
                                            ->label(__('keys.total_amount'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->step(0.01),
                                    ]),
                            ]),

                        Fieldset::make('Payment Status')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('paid_amount')
                                            ->label(__('keys.paid_amount'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->default(0),

                                        TextInput::make('refunded_amount')
                                            ->label(__('keys.refunded_amount'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->default(0),

                                        Select::make('currency')
                                            ->label(__('keys.currency'))
                                            ->options([
                                                'USD' => 'USD ($)',
                                                'EUR' => 'EUR (€)',
                                                'GBP' => 'GBP (£)',
                                                'AED' => 'AED (د.إ)',
                                                'SAR' => 'SAR (ر.س)',
                                            ])
                                            ->default('USD')
                                            ->required(),
                                    ]),
                            ]),
                    ]),

                Section::make('Transaction Timeline')
                    ->description(__('keys.important_dates_and_timestamps'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('confirmed_at')
                                    ->label(__('keys.confirmed_at'))
                                    ->nullable(),

                                DateTimePicker::make('completed_at')
                                    ->label(__('keys.completed_at'))
                                    ->nullable(),

                                DateTimePicker::make('cancelled_at')
                                    ->label(__('keys.cancelled_at'))
                                    ->nullable(),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->description(__('keys.notes_and_cancellation_details'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('keys.transaction_notes'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Textarea::make('cancellation_reason')
                            ->label(__('keys.cancellation_reason'))
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn ($get) => in_array($get('status'), [Transaction::STATUS_CANCELLED, Transaction::STATUS_REFUNDED]))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
