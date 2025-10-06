<?php

namespace App\Filament\Resources\Locations\Cities\Tables;

use App\Models\Location\Country;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('keys.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label(__('keys.country'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.code')
                    ->label(__('keys.country_code'))
                    ->badge()
                    ->toggleable(),

                TextColumn::make('branches_count')
                    ->label(__('keys.branches'))
                    ->counts('branches')
                    ->badge()
                    ->color('info'),

                TextColumn::make('latitude')
                    ->label(__('keys.latitude'))
                    ->numeric(6)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label(__('keys.longitude'))
                    ->numeric(6)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('keys.status'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('keys.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('keys.country'))
                    ->options(Country::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label(__('keys.status'))
                    ->placeholder('All cities')
                    ->trueLabel('Active cities')
                    ->falseLabel('Inactive cities'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('country.name', 'asc');
    }
}
