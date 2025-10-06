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
                            ->label(__('keys.transaction'))
                            ->relationship('transaction', 'transaction_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The transaction this payment is associated with'),

                        Select::make('user_id')
                            ->label(__('keys.user'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The user making this payment'),

                        Select::make('payment_method')
                            ->label(__('keys.payment_method'))
                            ->options(Payment::getMethods())
                            ->required()
                            ->helperText('The method used for payment'),

                        Select::make('payment_gateway')
                            ->label(__('keys.payment_gateway'))
                            ->options(Payment::getGateways())
                            ->required()
                            ->helperText('The payment gateway used to process this payment'),

                        Select::make('status')
                            ->label(__('keys.status'))
                            ->options(Payment::getStatuses())
                            ->default(Payment::STATUS_PENDING)
                            ->required()
                            ->helperText('Current status of the payment'),
                    ])
                    ->columns(2),

                Section::make('Gateway Details')
                    ->schema([
                        TextInput::make('gateway_transaction_id')
                            ->label(__('keys.id'))
                            ->maxLength(255)
                            ->helperText('Transaction ID from the payment gateway'),

                        TextInput::make('gateway_payment_id')
                            ->label(__('keys.id'))
                            ->maxLength(255)
                            ->helperText('Payment ID from the payment gateway'),

                        Textarea::make('gateway_response')
                            ->label(__('keys.gateway_response'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Raw response data from the payment gateway (JSON format)'),

                        Textarea::make('failure_reason')
                            ->label(__('keys.failure_reason'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Reason for payment failure (if applicable)'),
                    ])
                    ->columns(2),

                Section::make('Amount & Currency')
                    ->schema([
                        TextInput::make('amount')
                            ->label(__('keys.amount'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Payment amount'),

                        TextInput::make('currency')
                            ->label(__('keys.currency'))
                            ->default('USD')
                            ->required()
                            ->maxLength(3)
                            ->helperText('Currency code (e.g., USD, EUR)'),

                        TextInput::make('refunded_amount')
                            ->label(__('keys.refunded_amount'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Total amount refunded'),
                    ])
                    ->columns(3),

                Section::make('Timestamps')
                    ->schema([
                        DateTimePicker::make('processed_at')
                            ->label(__('keys.processed_at'))
                            ->helperText('When the payment was processed'),

                        DateTimePicker::make('refunded_at')
                            ->label(__('keys.refunded_at'))
                            ->helperText('When the refund was processed'),
                    ])
                    ->columns(2),

                Section::make('Additional Data')
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('keys.notes'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Additional notes about this payment'),

                        KeyValue::make('metadata')
                            ->label(__('keys.metadata'))
                            ->columnSpanFull()
                            ->helperText('Additional metadata in key-value format'),
                    ])
                    ->columns(1),
            ]);
    }
}
