<?php

namespace App\Filament\Resources\Locations\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('vendor.name'),
                TextColumn::make('country.name'),
                TextColumn::make('city.name'),
                TextColumn::make('address'),
                TextColumn::make('phone'),
                TextColumn::make('email'),
                TextColumn::make('manager_name'),
                BooleanColumn::make('is_active'),
            ])
            ->filters([
                SelectFilter::make('is_active'),
                SelectFilter::make('country_id'),
                SelectFilter::make('city_id'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
