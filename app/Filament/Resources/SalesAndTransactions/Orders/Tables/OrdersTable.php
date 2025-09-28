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
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status')
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
                    ->label('Payment')
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
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime()
                    ->placeholder('Not shipped')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime()
                    ->placeholder('Not delivered')
                    ->sortable()
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
                SelectFilter::make('status')
                    ->label('Order Status')
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
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                    ])
                    ->multiple(),

                Filter::make('recent_orders')
                    ->label('Recent Orders (Last 7 days)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Filter::make('high_value_orders')
                    ->label('High Value Orders ($100+)')
                    ->query(fn (Builder $query): Builder => $query->where('total_amount', '>=', 100))
                    ->toggle(),

                TernaryFilter::make('has_shipping_address')
                    ->label('Has Shipping Address')
                    ->query(function ($query, $data) {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('shipping_address');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('shipping_address');
                        }
                        return $query;
                    }),

                SelectFilter::make('user')
                    ->label('Customer')
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
