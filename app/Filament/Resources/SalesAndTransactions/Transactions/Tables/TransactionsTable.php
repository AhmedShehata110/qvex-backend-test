<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Tables;

use App\Models\Transaction\Transaction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_number')
                    ->label('Transaction #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'primary' => Transaction::TYPE_SALE,
                        'warning' => Transaction::TYPE_RENTAL,
                        'info' => Transaction::TYPE_LEASE,
                    ]),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'gray' => Transaction::STATUS_PENDING,
                        'info' => Transaction::STATUS_CONFIRMED,
                        'warning' => Transaction::STATUS_PAYMENT_PENDING,
                        'success' => [Transaction::STATUS_PAID, Transaction::STATUS_COMPLETED],
                        'primary' => Transaction::STATUS_IN_PROGRESS,
                        'danger' => [Transaction::STATUS_CANCELLED, Transaction::STATUS_REFUNDED, Transaction::STATUS_DISPUTED],
                    ]),

                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->buyer?->name),

                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->seller?->name),

                TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->vehicle?->title),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->money('USD')
                    ->sortable()
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('outstanding_amount')
                    ->label('Outstanding')
                    ->state(function ($record) {
                        $outstanding = ($record->total_amount ?? 0) - ($record->paid_amount ?? 0);

                        return $outstanding;
                    })
                    ->money('USD')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->weight(fn ($state) => $state > 0 ? 'bold' : 'normal'),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax_amount')
                    ->label('Tax')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('confirmed_at')
                    ->label('Confirmed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not confirmed')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('type')
                    ->label('Transaction Type')
                    ->options([
                        Transaction::TYPE_SALE => 'Sale',
                        Transaction::TYPE_RENTAL => 'Rental',
                        Transaction::TYPE_LEASE => 'Lease',
                    ]),

                SelectFilter::make('status')
                    ->label('Transaction Status')
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
                    ]),

                SelectFilter::make('buyer')
                    ->relationship('buyer', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('seller')
                    ->relationship('seller', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('currency')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                        'AED' => 'AED',
                        'SAR' => 'SAR',
                    ]),

                Filter::make('amount_range')
                    ->form([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('amount_from')
                                    ->label('Amount From')
                                    ->numeric()
                                    ->prefix('$'),
                                \Filament\Forms\Components\TextInput::make('amount_to')
                                    ->label('Amount To')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),

                Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\DatePicker::make('created_from')
                                    ->label('Created From'),
                                \Filament\Forms\Components\DatePicker::make('created_to')
                                    ->label('Created To'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('outstanding_balance')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('(total_amount - COALESCE(paid_amount, 0)) > 0')
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession();
    }
}
