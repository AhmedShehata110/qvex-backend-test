<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('keys.order'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('user.name')
                    ->label(__('keys.customer'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('total_amount')
                    ->label(__('keys.total'))
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label(__('keys.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'processing',
                        'warning' => 'shipped',
                        'success' => 'delivered',
                        'danger' => ['cancelled', 'refunded'],
                    ])
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label(__('keys.payment'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'info' => ['refunded', 'partially_refunded'],
                    ])
                    ->sortable(),

                TextColumn::make('order_date')
                    ->label(__('keys.order_date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('shipped_at')
                    ->label(__('keys.shipped'))
                    ->dateTime()
                    ->placeholder(__('keys.not_shipped'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('delivered_at')
                    ->label(__('keys.delivered'))
                    ->dateTime()
                    ->placeholder(__('keys.not_delivered'))
                    ->sortable()
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
                SelectFilter::make('status')
                    ->label(__('keys.status'))
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->label(__('keys.status'))
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                    ])
                    ->multiple(),

                Filter::make('recent_orders')
                    ->label(__('keys.recent_orders'))
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Filter::make('high_value_orders')
                    ->label(__('keys.high_value_orders'))
                    ->query(fn (Builder $query): Builder => $query->where('total_amount', '>=', 100))
                    ->toggle(),

                TernaryFilter::make('has_shipping_address')
                    ->label(__('keys.has_shipping_address'))
                    ->query(function ($query, $data) {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('shipping_address');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('shipping_address');
                        }
                        return $query;
                    }),

                SelectFilter::make('user')
                    ->label(__('keys.customer'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
