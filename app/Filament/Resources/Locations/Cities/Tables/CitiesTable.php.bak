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
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.code')
                    ->label('Country Code')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('branches_count')
                    ->label('Branches')
                    ->counts('branches')
                    ->badge()
                    ->color('info'),

                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->numeric(6)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->numeric(6)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label('Country')
                    ->options(Country::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Status')
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
