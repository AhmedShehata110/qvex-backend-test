<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Tables;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?name='.urlencode($record->name).
                               '&size=40&background=3B82F6&color=ffffff';
                    }),

                TextColumn::make('name')
                    ->label('Brand Name')
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

                TextColumn::make('country_origin')
                    ->label('Origin')
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
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('vehicle_models_count')
                    ->label('Models')
                    ->counts('vehicleModels')
                    ->badge()
                    ->color('secondary')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Sort')
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
                SelectFilter::make('country_origin')
                    ->label('Country of Origin')
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
                    ->label('Has Vehicles')
                    ->query(fn (Builder $query): Builder => $query->whereHas('vehicles'))
                    ->toggle(),

                Filter::make('popular_brands')
                    ->label('Popular Brands (5+ vehicles)')
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
