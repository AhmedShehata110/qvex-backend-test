<?php

namespace App\Filament\Resources\Communication\NewsletterSubscribers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterSubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email'),
                TextColumn::make('name'),
                BooleanColumn::make('is_subscribed'),
                TextColumn::make('subscription_source'),
                BooleanColumn::make('is_verified'),
            ])
            ->filters([
                SelectFilter::make('is_subscribed')
                    ->options([
                        '1' => 'Subscribed',
                        '0' => 'Unsubscribed',
                    ]),
                SelectFilter::make('is_verified')
                    ->options([
                        '1' => 'Verified',
                        '0' => 'Unverified',
                    ]),
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
