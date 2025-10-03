<?php

namespace App\Filament\Resources\VehicleManagement\Properties\Tables;

use App\Models\Vehicle\VehicleFeature;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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
                SpatieMediaLibraryImageColumn::make('icon')
                    ->label('Icon')
                    ->circular()
                    ->size(40)
                    ->collection('icons'),

                TextColumn::make('name')
                    ->label('Feature Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('name_ar')
                    ->label('Arabic Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category')
                    ->label('Category')
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
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('active_vehicle_count')
                    ->label('Active Vehicles')
                    ->getStateUsing(fn ($record) => $record->active_vehicle_count)
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(VehicleFeature::getCategories())
                    ->multiple(),

                TernaryFilter::make('is_premium')
                    ->label('Premium Feature'),

                SelectFilter::make('has_vehicles')
                    ->label('Has Vehicles')
                    ->options([
                        'yes' => 'Has Vehicles',
                        'no' => 'No Vehicles',
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
