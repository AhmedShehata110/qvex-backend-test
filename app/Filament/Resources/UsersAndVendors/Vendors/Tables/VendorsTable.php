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
                    ->label(__('keys.vendor_name'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('owner.name')
                    ->label(__('keys.vendor_owner'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('owner.email')
                    ->label(__('keys.email'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('owner.phone')
                    ->label(__('keys.work_phone'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('vehicles_count')
                    ->label(__('keys.vehicles'))
                    ->counts('vehicles')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('website')
                    ->label(__('keys.website_url'))
                    ->limit(30)
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('keys.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('keys.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('keys.status'))
                    ->placeholder(__('keys.all_vendors'))
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
