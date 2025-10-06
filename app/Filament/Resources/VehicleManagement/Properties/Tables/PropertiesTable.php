<?php

namespace App\Filament\Resources\VehicleManagement\Properties\Tables;

use App\Filament\Tables\Columns\MediaImageColumn;
use App\Models\Vehicle\VehicleFeature;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                MediaImageColumn::make('icon')
                    ->label(__('keys.icon'))
                    ->circular()
                    ->size(40)
                    ->collection('icons'),

                TextColumn::make('name')
                    ->label(__('keys.feature_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('name_ar')
                    ->label(__('keys.arabic_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label(__('keys.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category')
                    ->label(__('keys.category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => VehicleFeature::getCategories()[$state] ?? ucfirst($state))
                    ->colors([
                        'primary' => [VehicleFeature::CATEGORY_SAFETY, VehicleFeature::CATEGORY_TECHNOLOGY],
                        'success' => [VehicleFeature::CATEGORY_COMFORT, VehicleFeature::CATEGORY_PERFORMANCE],
                        'warning' => [VehicleFeature::CATEGORY_EXTERIOR, VehicleFeature::CATEGORY_INTERIOR],
                        'info' => [VehicleFeature::CATEGORY_AUDIO],
                        'gray' => VehicleFeature::CATEGORY_OTHER,
                    ])
                    ->sortable(),

                TextColumn::make('vehicle_count')
                    ->label(__('keys.vehicles'))
                    ->counts('vehicles')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('active_vehicle_count')
                    ->label(__('keys.active_vehicles'))
                    ->getStateUsing(fn ($record) => $record->active_vehicle_count)
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_premium')
                    ->label(__('keys.premium'))
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('sort_order')
                    ->label(__('keys.sort'))
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('category')
                    ->label(__('keys.category'))
                    ->options(VehicleFeature::getCategories())
                    ->multiple(),

                TernaryFilter::make('is_premium')
                    ->label(__('keys.premium_feature')),

                SelectFilter::make('has_vehicles')
                    ->label(__('keys.has_vehicles'))
                    ->options([
                        'yes' => __('keys.yes'),
                        'no' => __('keys.no'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'yes') {
                            $query->whereHas('vehicles');
                        } elseif ($data['value'] === 'no') {
                            $query->whereDoesntHave('vehicles');
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
