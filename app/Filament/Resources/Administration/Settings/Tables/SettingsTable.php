<?php

namespace App\Filament\Resources\Administration\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key'),
                TextColumn::make('value'),
                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'string',
                        'success' => 'integer',
                        'warning' => 'boolean',
                        'danger' => 'json',
                        'gray' => 'text',
                    ]),
                TextColumn::make('group'),
                BooleanColumn::make('is_public'),
            ])
            ->filters([
                SelectFilter::make('type'),
                SelectFilter::make('group'),
                SelectFilter::make('is_public'),
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
