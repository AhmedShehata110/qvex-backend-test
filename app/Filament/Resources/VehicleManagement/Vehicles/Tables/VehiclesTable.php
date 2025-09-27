<?php

namespace App\Filament\Resources\VehicleManagement\Vehicles\Tables;

use App\Models\Vehicle\Vehicle;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl('/images/default-vehicle.png')
                    ->size(60),

                TextColumn::make('title')
                    ->label('Vehicle Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('make.name')
                    ->label('Make')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('model.name')
                    ->label('Model')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                BadgeColumn::make('condition')
                    ->label('Condition')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'success' => Vehicle::CONDITION_NEW,
                        'warning' => Vehicle::CONDITION_CERTIFIED_PREOWNED,
                        'info' => Vehicle::CONDITION_USED,
                        'danger' => Vehicle::CONDITION_SALVAGE,
                    ]),

                BadgeColumn::make('availability_type')
                    ->label('Availability')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Vehicle::AVAILABILITY_SALE => 'For Sale',
                        Vehicle::AVAILABILITY_RENT => 'For Rent',
                        Vehicle::AVAILABILITY_BOTH => 'Sale & Rent',
                        default => ucfirst($state)
                    })
                    ->colors([
                        'primary' => Vehicle::AVAILABILITY_SALE,
                        'warning' => Vehicle::AVAILABILITY_RENT,
                        'success' => Vehicle::AVAILABILITY_BOTH,
                    ]),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'success' => Vehicle::STATUS_ACTIVE,
                        'warning' => Vehicle::STATUS_PENDING_APPROVAL,
                        'danger' => [Vehicle::STATUS_SOLD, Vehicle::STATUS_INACTIVE],
                        'gray' => Vehicle::STATUS_DRAFT,
                        'info' => Vehicle::STATUS_RENTED,
                    ]),

                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mileage')
                    ->label('Mileage')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state).' km' : 'N/A')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('inquiry_count')
                    ->label('Inquiries')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Listed')
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
                    ->preload(),

                SelectFilter::make('condition')
                    ->options([
                        Vehicle::CONDITION_NEW => 'New',
                        Vehicle::CONDITION_USED => 'Used',
                        Vehicle::CONDITION_CERTIFIED_PREOWNED => 'Certified Pre-owned',
                        Vehicle::CONDITION_SALVAGE => 'Salvage',
                    ]),

                SelectFilter::make('availability_type')
                    ->label('Availability')
                    ->options([
                        Vehicle::AVAILABILITY_SALE => 'For Sale',
                        Vehicle::AVAILABILITY_RENT => 'For Rent',
                        Vehicle::AVAILABILITY_BOTH => 'Both',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        Vehicle::STATUS_ACTIVE => 'Active',
                        Vehicle::STATUS_DRAFT => 'Draft',
                        Vehicle::STATUS_SOLD => 'Sold',
                        Vehicle::STATUS_RENTED => 'Rented',
                        Vehicle::STATUS_INACTIVE => 'Inactive',
                        Vehicle::STATUS_PENDING_APPROVAL => 'Pending Approval',
                    ]),

                SelectFilter::make('vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All vehicles')
                    ->trueLabel('Featured vehicles')
                    ->falseLabel('Non-featured vehicles'),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All vehicles')
                    ->trueLabel('Active vehicles')
                    ->falseLabel('Inactive vehicles'),

                Filter::make('price_range')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('price_from')
                                    ->label('Price From')
                                    ->numeric()
                                    ->prefix('$'),
                                \Filament\Forms\Components\TextInput::make('price_to')
                                    ->label('Price To')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['price_from'] ?? null) {
                            $indicators['price_from'] = 'Price from: $'.number_format($data['price_from']);
                        }
                        if ($data['price_to'] ?? null) {
                            $indicators['price_to'] = 'Price to: $'.number_format($data['price_to']);
                        }

                        return $indicators;
                    }),

                Filter::make('year_range')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('year_from')
                                    ->label('Year From')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1),
                                \Filament\Forms\Components\TextInput::make('year_to')
                                    ->label('Year To')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['year_from'],
                                fn (Builder $query, $year): Builder => $query->where('year', '>=', $year),
                            )
                            ->when(
                                $data['year_to'],
                                fn (Builder $query, $year): Builder => $query->where('year', '<=', $year),
                            );
                    }),
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
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession();
    }
}
