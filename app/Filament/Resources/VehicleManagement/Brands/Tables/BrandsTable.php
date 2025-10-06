<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Tables;

use App\Filament\Tables\Columns\MediaImageColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                MediaImageColumn::make('logo')
                    ->label(__('keys.logo'))
                    ->circular()
                    ->size(40)
                    ->collection('logos'),
                TextColumn::make('name')
                    ->label(__('keys.brand_name'))
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

                TextColumn::make('country_origin')
                    ->label(__('keys.origin'))
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'germany' => '🇩🇪 Germany',
                            'japan' => '🇯🇵 Japan',
                            'usa' => '🇺🇸 USA',
                            'uk' => '🇬🇧 UK',
                            'france' => '🇫🇷 France',
                            'italy' => '🇮🇹 Italy',
                            'south_korea' => '🇰🇷 S. Korea',
                            'sweden' => '🇸🇪 Sweden',
                            'spain' => '🇪🇸 Spain',
                            'czech_republic' => '🇨🇿 Czech Rep.',
                            'china' => '🇨🇳 China',
                            'india' => '🇮🇳 India',
                            'malaysia' => '🇲🇾 Malaysia',
                            default => '🌍 Other',
                        };
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'germany', 'japan', 'usa', 'uk' => 'success',
                            'france', 'italy', 'sweden' => 'info',
                            'south_korea', 'china' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                TextColumn::make('vehicles_count')
                    ->label(__('keys.vehicles'))
                    ->counts('vehicles')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('vehicle_models_count')
                    ->label(__('keys.models'))
                    ->counts('vehicleModels')
                    ->badge()
                    ->color('secondary')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label(__('keys.sort'))
                    ->sortable()
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
                SelectFilter::make('country_origin')
                    ->label(__('keys.country_of_origin'))
                    ->options([
                        'germany' => '🇩🇪 Germany',
                        'japan' => '🇯🇵 Japan',
                        'usa' => '🇺🇸 United States',
                        'uk' => '🇬🇧 United Kingdom',
                        'france' => '🇫🇷 France',
                        'italy' => '🇮🇹 Italy',
                        'south_korea' => '🇰🇷 South Korea',
                        'sweden' => '🇸🇪 Sweden',
                        'spain' => '🇪🇸 Spain',
                        'czech_republic' => '🇨🇿 Czech Republic',
                        'china' => '🇨🇳 China',
                        'india' => '🇮🇳 India',
                        'malaysia' => '🇲🇾 Malaysia',
                        'other' => '🌍 Other',
                    ]),

                Filter::make('has_vehicles')
                    ->label(__('keys.has_vehicles'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('vehicles'))
                    ->toggle(),

                Filter::make('popular_brands')
                    ->label(__('keys.popular_brands'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('vehicles', function ($q) {
                        $q->havingRaw('COUNT(*) >= 5');
                    }))
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
