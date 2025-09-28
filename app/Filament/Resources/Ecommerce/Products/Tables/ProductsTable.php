<?php

namespace App\Filament\Resources\Ecommerce\Products\Tables;

use App\Models\Vehicle\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('make.name')
                    ->label('Make')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('model.name')
                    ->label('Model')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),

                BadgeColumn::make('condition')
                    ->label('Condition')
                    ->colors([
                        'success' => Vehicle::CONDITION_NEW,
                        'warning' => Vehicle::CONDITION_USED,
                        'info' => Vehicle::CONDITION_CERTIFIED_PREOWNED,
                        'danger' => Vehicle::CONDITION_SALVAGE,
                    ])
                    ->icons([
                        'heroicon-o-star' => Vehicle::CONDITION_NEW,
                        'heroicon-o-clock' => Vehicle::CONDITION_USED,
                        'heroicon-o-shield-check' => Vehicle::CONDITION_CERTIFIED_PREOWNED,
                        'heroicon-o-exclamation-triangle' => Vehicle::CONDITION_SALVAGE,
                    ]),

                BadgeColumn::make('availability_type')
                    ->label('Availability')
                    ->colors([
                        'success' => Vehicle::AVAILABILITY_SALE,
                        'info' => Vehicle::AVAILABILITY_RENT,
                        'warning' => Vehicle::AVAILABILITY_BOTH,
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            Vehicle::AVAILABILITY_SALE => 'For Sale',
                            Vehicle::AVAILABILITY_RENT => 'For Rent',
                            Vehicle::AVAILABILITY_BOTH => 'Sale & Rent',
                            default => $state,
                        };
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => Vehicle::STATUS_DRAFT,
                        'warning' => Vehicle::STATUS_ACTIVE,
                        'success' => Vehicle::STATUS_SOLD,
                        'info' => Vehicle::STATUS_RENTED,
                        'danger' => Vehicle::STATUS_INACTIVE,
                        'primary' => Vehicle::STATUS_PENDING_APPROVAL,
                    ])
                    ->formatStateUsing(function ($state) {
                        return Vehicle::getStatuses()[$state] ?? $state;
                    }),

                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('city')
                    ->label('Location')
                    ->formatStateUsing(function ($record) {
                        return $record->city . ', ' . $record->state;
                    })
                    ->searchable(['city', 'state']),

                TextColumn::make('mileage')
                    ->label('Mileage')
                    ->formatStateUsing(function ($record) {
                        return $record->mileage ? number_format($record->mileage) . ' ' . $record->mileage_unit : '-';
                    })
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                IconColumn::make('is_urgent')
                    ->label('Urgent')
                    ->boolean(),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('inquiry_count')
                    ->label('Inquiries')
                    ->sortable()
                    ->alignCenter(),

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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Vehicle::getStatuses())
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label('Condition')
                    ->options(Vehicle::getConditions())
                    ->multiple(),

                SelectFilter::make('availability_type')
                    ->label('Availability')
                    ->options(Vehicle::getAvailabilityTypes())
                    ->multiple(),

                SelectFilter::make('make_id')
                    ->label('Make')
                    ->relationship('make', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('model_id')
                    ->label('Model')
                    ->relationship('model', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),

                TernaryFilter::make('is_urgent')
                    ->label('Urgent'),

                SelectFilter::make('year')
                    ->label('Year')
                    ->options(function () {
                        $currentYear = date('Y') + 1;
                        $years = [];
                        for ($year = $currentYear; $year >= 1900; $year--) {
                            $years[$year] = $year;
                        }
                        return $years;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
