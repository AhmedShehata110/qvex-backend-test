<?php

namespace App\Filament\Resources\Locations\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('type'),
                TextColumn::make('label'),
                TextColumn::make('first_name'),
                TextColumn::make('last_name'),
                TextColumn::make('address_line_1'),
                TextColumn::make('city'),
                TextColumn::make('state'),
                TextColumn::make('postal_code'),
                TextColumn::make('country'),
                BooleanColumn::make('is_default'),
            ])
            ->filters([
                SelectFilter::make('type'),
                SelectFilter::make('country'),
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
