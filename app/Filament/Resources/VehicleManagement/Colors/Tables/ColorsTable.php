<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ColorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Color Name')
                    ->searchable()
                    ->sortable(),

                ColorColumn::make('hex_code')
                    ->label('Color')
                    ->copyable()
                    ->copyMessage('Color code copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('hex_code')
                    ->label('Hex Code')
                    ->copyable()
                    ->copyMessage('Hex code copied!')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rgb_value')
                    ->label('RGB Value')
                    ->copyable()
                    ->copyMessage('RGB value copied!')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'exterior' => 'success',
                        'interior' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_metallic')
                    ->label('Metallic')
                    ->boolean()
                    ->trueIcon('heroicon-o-sparkles')
                    ->falseIcon('heroicon-o-minus'),

                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
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
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'exterior' => 'Exterior',
                        'interior' => 'Interior',
                    ])
                    ->multiple(),

                TernaryFilter::make('is_metallic')
                    ->label('Metallic'),

                TernaryFilter::make('is_popular')
                    ->label('Popular'),
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
            ->defaultSort('sort_order')
            ->paginated([10, 25, 50, 100]);
    }
}
