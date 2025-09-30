<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business_name')
                    ->label('Vendor Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('owner.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('owner.phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('vehicles_count')
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('website')
                    ->label('Website')
                    ->limit(30)
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All vendors')
                    ->trueLabel('Active vendors')
                    ->falseLabel('Inactive vendors'),

                SelectFilter::make('owner')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
