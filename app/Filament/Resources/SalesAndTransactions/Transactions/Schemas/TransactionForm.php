<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Schemas;

use App\Models\Transaction\Transaction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
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
                    ->description('Basic transaction information and participants')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('transaction_number')
                                    ->label('Transaction Number')
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->helperText('Auto-generated unique identifier'),

                                Select::make('type')
                                    ->label('Transaction Type')
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
                                    ->label('Buyer')
                                    ->relationship('buyer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('seller_id')
                                    ->label('Seller')
                                    ->relationship('seller', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('vehicle_id')
                                    ->label('Vehicle')
                                    ->relationship('vehicle', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Select::make('status')
                            ->label('Status')
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
                    ->description('Transaction amounts and payment details')
                    ->schema([
                        Fieldset::make('Amount Breakdown')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->step(0.01),

                                        TextInput::make('tax_amount')
                                            ->label('Tax Amount')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('commission_amount')
                                            ->label('Commission')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),

                                        TextInput::make('total_amount')
                                            ->label('Total Amount')
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
                                            ->label('Paid Amount')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->default(0),

                                        TextInput::make('refunded_amount')
                                            ->label('Refunded Amount')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->default(0),

                                        Select::make('currency')
                                            ->label('Currency')
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
                    ->description('Important dates and timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('confirmed_at')
                                    ->label('Confirmed At')
                                    ->nullable(),

                                DateTimePicker::make('completed_at')
                                    ->label('Completed At')
                                    ->nullable(),

                                DateTimePicker::make('cancelled_at')
                                    ->label('Cancelled At')
                                    ->nullable(),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->description('Notes and cancellation details')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Transaction Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Textarea::make('cancellation_reason')
                            ->label('Cancellation Reason')
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn ($get) => in_array($get('status'), [Transaction::STATUS_CANCELLED, Transaction::STATUS_REFUNDED]))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
