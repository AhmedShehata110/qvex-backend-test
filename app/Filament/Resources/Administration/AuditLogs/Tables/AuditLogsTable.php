<?php

namespace App\Filament\Resources\Administration\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model_type'),
                TextColumn::make('model_id'),
                BadgeColumn::make('event')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                    ]),
                TextColumn::make('user.name'),
                TextColumn::make('ip_address'),
                TextColumn::make('url'),
                TextColumn::make('method'),
                TextColumn::make('occurred_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('event'),
                SelectFilter::make('model_type'),
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
