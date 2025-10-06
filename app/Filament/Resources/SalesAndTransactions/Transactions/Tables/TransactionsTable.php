<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Tables;

use App\Models\Transaction\Transaction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->label(__('keys.transaction_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('type')
                    ->label(__('keys.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'primary' => Transaction::TYPE_SALE,
                        'warning' => Transaction::TYPE_RENTAL,
                        'info' => Transaction::TYPE_LEASE,
                    ]),

                TextColumn::make('status')
                    ->label(__('keys.status'))
                    ->badge()
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
                    ->label(__('keys.buyer'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->buyer?->name),

                TextColumn::make('seller.name')
                    ->label(__('keys.seller'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->seller?->name),

                TextColumn::make('vehicle.title')
                    ->label(__('keys.vehicle'))
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->vehicle?->title),

                TextColumn::make('total_amount')
                    ->label(__('keys.subtotal'))
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('paid_amount')
                    ->label(__('keys.paid_amount'))
                    ->money('USD')
                    ->sortable()
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('outstanding_amount')
                    ->label(__('keys.outstanding'))
                    ->state(function ($record) {
                        $outstanding = ($record->total_amount ?? 0) - ($record->paid_amount ?? 0);

                        return $outstanding;
                    })
                    ->money('USD')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->weight(fn ($state) => $state > 0 ? 'bold' : 'normal'),

                TextColumn::make('currency')
                    ->label(__('keys.currency'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('commission_amount')
                    ->label(__('keys.commission'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax_amount')
                    ->label(__('keys.tax_amount'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('confirmed_at')
                    ->label(__('keys.confirmed'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('keys.not_confirmed'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label(__('keys.completed'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('keys.not_completed'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('keys.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('keys.type'))
                    ->options([
                        Transaction::TYPE_SALE => 'Sale',
                        Transaction::TYPE_RENTAL => 'Rental',
                        Transaction::TYPE_LEASE => 'Lease',
                    ]),

                SelectFilter::make('status')
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
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('amount_from')
                                    ->label(__('keys.amount_from'))
                                    ->numeric()
                                    ->prefix('$'),
                                \Filament\Forms\Components\TextInput::make('amount_to')
                                    ->label(__('keys.amount_to'))
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
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\DatePicker::make('created_from')
                                    ->label(__('keys.created_from')),
                                \Filament\Forms\Components\DatePicker::make('created_to')
                                    ->label(__('keys.created_to')),
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
