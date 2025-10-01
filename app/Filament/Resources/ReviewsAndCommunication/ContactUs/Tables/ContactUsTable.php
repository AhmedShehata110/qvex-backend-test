<?php

namespace App\Filament\Resources\ReviewsAndCommunication\ContactUs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactUsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('subject'),
                BadgeColumn::make('priority')
                    ->colors([
                        'success' => 1,
                        'warning' => 2,
                        'danger' => 3,
                    ]),
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'open',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'gray' => 'closed',
                    ]),
                TextColumn::make('category'),
            ])
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('priority'),
                SelectFilter::make('category'),
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
