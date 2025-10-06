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
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest Cart'),

                TextColumn::make('session_id')
                    ->label('Session ID')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_items')
                    ->label('Items')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Currency')
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
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

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
