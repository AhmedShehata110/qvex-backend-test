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
                    ->label(__('keys.color_name'))
                    ->searchable()
                    ->sortable(),

                ColorColumn::make('hex_code')
                    ->label(__('keys.color'))
                    ->copyable()
                    ->copyMessage('Color code copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('hex_code')
                    ->label(__('keys.hex_code'))
                    ->copyable()
                    ->copyMessage('Hex code copied!')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rgb_value')
                    ->label(__('keys.rgb_value'))
                    ->copyable()
                    ->copyMessage('RGB value copied!')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label(__('keys.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'exterior' => 'success',
                        'interior' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_metallic')
                    ->label(__('keys.metallic'))
                    ->boolean()
                    ->trueIcon('heroicon-o-sparkles')
                    ->falseIcon('heroicon-o-minus'),

                IconColumn::make('is_popular')
                    ->label(__('keys.popular'))
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),

                TextColumn::make('sort_order')
                    ->label(__('keys.sort'))
                    ->numeric()
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
                SelectFilter::make('type')
                    ->label(__('keys.type'))
                    ->options([
                        'exterior' => __('keys.exterior'),
                        'interior' => __('keys.interior'),
                    ])
                    ->multiple(),

                TernaryFilter::make('is_metallic')
                    ->label(__('keys.metallic')),

                TernaryFilter::make('is_popular')
                    ->label(__('keys.popular')),
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
