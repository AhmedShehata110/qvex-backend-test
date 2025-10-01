<?php

namespace App\Filament\Resources\Utilities\FailedJobs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FailedJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid'),
                TextColumn::make('queue'),
                TextColumn::make('connection'),
                TextColumn::make('failed_at')
                    ->dateTime(),
                TextColumn::make('retry_count'),
            ])
            ->filters([
                SelectFilter::make('queue'),
                SelectFilter::make('connection'),
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
