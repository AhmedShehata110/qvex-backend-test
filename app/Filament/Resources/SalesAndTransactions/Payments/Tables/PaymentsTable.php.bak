<?php

namespace App\Filament\Resources\SalesAndTransactions\Payments\Tables;

use App\Models\Transaction\Payment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('transaction.transaction_number')
                    ->label('Transaction')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->tooltip('Click to copy'),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->tooltip('Click to copy'),

                BadgeColumn::make('payment_method')
                    ->label('Method')
                    ->colors([
                        'primary' => Payment::METHOD_CREDIT_CARD,
                        'success' => Payment::METHOD_DEBIT_CARD,
                        'warning' => Payment::METHOD_BANK_TRANSFER,
                        'info' => Payment::METHOD_DIGITAL_WALLET,
                        'gray' => Payment::METHOD_CASH,
                    ])
                    ->formatStateUsing(fn (string $state): string => Payment::getMethods()[$state] ?? $state),

                BadgeColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->colors([
                        'primary' => Payment::GATEWAY_STRIPE,
                        'success' => Payment::GATEWAY_PAYPAL,
                        'warning' => Payment::GATEWAY_RAZORPAY,
                        'info' => Payment::GATEWAY_FLUTTERWAVE,
                    ])
                    ->formatStateUsing(fn (string $state): string => Payment::getGateways()[$state] ?? $state),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->sortable()
                    ->alignCenter(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => Payment::STATUS_PENDING,
                        'warning' => Payment::STATUS_PROCESSING,
                        'success' => Payment::STATUS_COMPLETED,
                        'danger' => Payment::STATUS_FAILED,
                        'secondary' => Payment::STATUS_CANCELLED,
                        'info' => Payment::STATUS_REFUNDED,
                        'primary' => Payment::STATUS_PARTIALLY_REFUNDED,
                    ])
                    ->formatStateUsing(fn (string $state): string => Payment::getStatuses()[$state] ?? $state),

                TextColumn::make('gateway_transaction_id')
                    ->label('Gateway TX ID')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Click to copy')
                    ->limit(20),

                TextColumn::make('processed_at')
                    ->label('Processed At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('refunded_amount')
                    ->label('Refunded')
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Payment::getStatuses())
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options(Payment::getMethods())
                    ->multiple(),

                SelectFilter::make('payment_gateway')
                    ->label('Payment Gateway')
                    ->options(Payment::getGateways())
                    ->multiple(),

                Filter::make('processed_at')
                    ->form([
                        DatePicker::make('processed_from')
                            ->label('Processed From'),
                        DatePicker::make('processed_until')
                            ->label('Processed Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['processed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '>=', $date),
                            )
                            ->when(
                                $data['processed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '<=', $date),
                            );
                    }),

                Filter::make('amount_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->label('Amount From')
                            ->numeric()
                            ->minValue(0),
                        \Filament\Forms\Components\TextInput::make('amount_until')
                            ->label('Amount Until')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_until'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
