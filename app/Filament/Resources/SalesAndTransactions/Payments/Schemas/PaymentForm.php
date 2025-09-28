<?php

namespace App\Filament\Resources\SalesAndTransactions\Payments\Schemas;

use App\Models\Transaction\Payment;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->schema([
                        Select::make('transaction_id')
                            ->label('Transaction')
                            ->relationship('transaction', 'transaction_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The transaction this payment is associated with'),

                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The user making this payment'),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(Payment::getMethods())
                            ->required()
                            ->helperText('The method used for payment'),

                        Select::make('payment_gateway')
                            ->label('Payment Gateway')
                            ->options(Payment::getGateways())
                            ->required()
                            ->helperText('The payment gateway used to process this payment'),

                        Select::make('status')
                            ->label('Status')
                            ->options(Payment::getStatuses())
                            ->default(Payment::STATUS_PENDING)
                            ->required()
                            ->helperText('Current status of the payment'),
                    ])
                    ->columns(2),

                Section::make('Gateway Details')
                    ->schema([
                        TextInput::make('gateway_transaction_id')
                            ->label('Gateway Transaction ID')
                            ->maxLength(255)
                            ->helperText('Transaction ID from the payment gateway'),

                        TextInput::make('gateway_payment_id')
                            ->label('Gateway Payment ID')
                            ->maxLength(255)
                            ->helperText('Payment ID from the payment gateway'),

                        Textarea::make('gateway_response')
                            ->label('Gateway Response')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Raw response data from the payment gateway (JSON format)'),

                        Textarea::make('failure_reason')
                            ->label('Failure Reason')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Reason for payment failure (if applicable)'),
                    ])
                    ->columns(2),

                Section::make('Amount & Currency')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Payment amount'),

                        TextInput::make('currency')
                            ->label('Currency')
                            ->default('USD')
                            ->required()
                            ->maxLength(3)
                            ->helperText('Currency code (e.g., USD, EUR)'),

                        TextInput::make('refunded_amount')
                            ->label('Refunded Amount')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Total amount refunded'),
                    ])
                    ->columns(3),

                Section::make('Timestamps')
                    ->schema([
                        DateTimePicker::make('processed_at')
                            ->label('Processed At')
                            ->helperText('When the payment was processed'),

                        DateTimePicker::make('refunded_at')
                            ->label('Refunded At')
                            ->helperText('When the refund was processed'),
                    ])
                    ->columns(2),

                Section::make('Additional Data')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Additional notes about this payment'),

                        KeyValue::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull()
                            ->helperText('Additional metadata in key-value format'),
                    ])
                    ->columns(1),
            ]);
    }
}
