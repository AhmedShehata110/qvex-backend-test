<?php

namespace App\Filament\Resources\SalesAndTransactions\Carts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('keys.user'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest Cart'),

                TextColumn::make('session_id')
                    ->label(__('keys.session_id'))
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_items')
                    ->label(__('keys.items'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_amount')
                    ->label(__('keys.total'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label(__('keys.currency'))
                    ->badge()
                    ->color('success'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'abandoned' => 'warning',
                        'converted' => 'info',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('expires_at')
                    ->label(__('keys.expires'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

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
                    ->options([
                        'active' => 'Active',
                        'abandoned' => 'Abandoned',
                        'converted' => 'Converted to Order',
                        'expired' => 'Expired',
                    ])
                    ->placeholder('All statuses'),

                SelectFilter::make('currency')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                        'CAD' => 'CAD',
                        'AUD' => 'AUD',
                    ])
                    ->placeholder('All currencies'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
