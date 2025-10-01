<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Messages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject'),
                TextColumn::make('sender.name'),
                TextColumn::make('recipient.name'),
                BadgeColumn::make('message_type')
                    ->colors([
                        'primary' => 'email',
                        'success' => 'sms',
                        'warning' => 'in_app',
                    ]),
                BadgeColumn::make('priority')
                    ->colors([
                        'success' => 1,
                        'warning' => 2,
                        'danger' => 3,
                    ]),
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'sent',
                        'warning' => 'delivered',
                        'success' => 'read',
                        'danger' => 'failed',
                    ]),
                BooleanColumn::make('is_read'),
                BooleanColumn::make('is_starred'),
            ])
            ->filters([
                SelectFilter::make('message_type'),
                SelectFilter::make('priority'),
                SelectFilter::make('status'),
                SelectFilter::make('is_read'),
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
