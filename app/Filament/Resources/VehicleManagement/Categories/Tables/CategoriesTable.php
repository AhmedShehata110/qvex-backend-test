<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Tables;

use App\Models\Vehicle\VehicleModel;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('make.name')
                    ->label('Make')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Model Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name_ar')
                    ->label('Arabic Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('body_type')
                    ->label('Body Type')
                    ->formatStateUsing(fn (string $state): string => VehicleModel::getBodyTypes()[$state] ?? ucfirst($state))
                    ->colors([
                        'primary' => [VehicleModel::BODY_TYPE_SEDAN, VehicleModel::BODY_TYPE_COUPE],
                        'success' => [VehicleModel::BODY_TYPE_SUV, VehicleModel::BODY_TYPE_PICKUP],
                        'warning' => [VehicleModel::BODY_TYPE_HATCHBACK, VehicleModel::BODY_TYPE_WAGON],
                        'info' => [VehicleModel::BODY_TYPE_CONVERTIBLE, VehicleModel::BODY_TYPE_VAN],
                        'danger' => [VehicleModel::BODY_TYPE_TRUCK, VehicleModel::BODY_TYPE_MOTORCYCLE],
                        'gray' => VehicleModel::BODY_TYPE_OTHER,
                    ]),

                TextColumn::make('year_start')
                    ->label('Start Year')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('year_end')
                    ->label('End Year')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('Present')
                    ->toggleable(),

                TextColumn::make('year_range')
                    ->label('Production Period')
                    ->badge()
                    ->color(fn ($record) => $record->isCurrent() ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('vehicle_count')
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('active_vehicle_count')
                    ->label('Active Vehicles')
                    ->counts('activeVehicles')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('addedBy.name')
                    ->label('Added By')
                    ->searchable()
                    ->sortable()
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
                SelectFilter::make('make')
                    ->relationship('make', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('body_type')
                    ->options(VehicleModel::getBodyTypes())
                    ->multiple(),

                SelectFilter::make('year_start')
                    ->options(function () {
                        $years = [];
                        $currentYear = (int) date('Y');
                        for ($year = 1980; $year <= $currentYear + 5; $year += 10) {
                            $years[$year] = $year.'s';
                        }

                        return $years;
                    })
                    ->query(function ($query, array $data) {
                        if (! empty($data['values'])) {
                            $query->where(function ($query) use ($data) {
                                foreach ($data['values'] as $decade) {
                                    $query->orWhereBetween('year_start', [$decade, $decade + 9]);
                                }
                            });
                        }
                    }),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Models')
                    ->trueLabel('Active Models')
                    ->falseLabel('Inactive Models'),

                TernaryFilter::make('current_production')
                    ->label('Production Status')
                    ->placeholder('All Models')
                    ->trueLabel('In Production')
                    ->falseLabel('Discontinued')
                    ->query(function ($query, $data) {
                        if ($data['value'] === true) {
                            $query->current();
                        } elseif ($data['value'] === false) {
                            $query->where('year_end', '<', date('Y'));
                        }
                    }),

                SelectFilter::make('addedBy')
                    ->label('Added By')
                    ->relationship('addedBy', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('make.name', 'asc')
            ->reorderable('sort_order');
    }
}
