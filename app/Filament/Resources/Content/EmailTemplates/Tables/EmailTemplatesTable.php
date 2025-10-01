<?php

namespace App\Filament\Resources\Content\EmailTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('category'),
                TextColumn::make('type'),
                TextColumn::make('subject'),
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),
                BooleanColumn::make('is_default'),
                TextColumn::make('usage_count'),
            ])
            ->filters([
                SelectFilter::make('category'),
                SelectFilter::make('status'),
                SelectFilter::make('type'),
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
