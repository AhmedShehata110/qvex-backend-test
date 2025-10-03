<?php

namespace App\Filament\Resources\VehicleManagement\Properties\Schemas;

use App\Models\Vehicle\VehicleFeature;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Feature Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Feature Name')
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('primary'),

                                TextEntry::make('name_ar')
                                    ->label('Arabic Name')
                                    ->placeholder('Not provided'),

                                TextEntry::make('slug')
                                    ->label('Slug')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('category')
                                    ->label('Category')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => VehicleFeature::getCategories()[$state] ?? ucfirst($state))
                                    ->color(function (string $state): string {
                                        return match ($state) {
                                            VehicleFeature::CATEGORY_SAFETY, VehicleFeature::CATEGORY_TECHNOLOGY => 'primary',
                                            VehicleFeature::CATEGORY_COMFORT, VehicleFeature::CATEGORY_PERFORMANCE => 'success',
                                            VehicleFeature::CATEGORY_EXTERIOR, VehicleFeature::CATEGORY_INTERIOR => 'warning',
                                            VehicleFeature::CATEGORY_AUDIO => 'info',
                                            default => 'gray',
                                        };
                                    }),

                                Grid::make(2)
                                    ->schema([
                                        IconEntry::make('is_premium')
                                            ->label('Premium Feature')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-star')
                                            ->falseIcon('heroicon-o-minus')
                                            ->trueColor('warning')
                                            ->falseColor('gray'),

                                        TextEntry::make('sort_order')
                                            ->label('Sort Order')
                                            ->badge()
                                            ->color('secondary'),
                                    ]),
                            ]),

                        Section::make('Feature Statistics')
                            ->schema([
                                TextEntry::make('vehicle_count')
                                    ->label('Total Vehicles')
                                    ->getStateUsing(fn ($record) => $record->vehicle_count)
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large),

                                TextEntry::make('active_vehicle_count')
                                    ->label('Active Vehicles')
                                    ->getStateUsing(fn ($record) => $record->active_vehicle_count)
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                Section::make('Feature Icon')
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('icon')
                            ->hiddenLabel()
                            ->size(100)
                            ->collection('icons'),
                    ])
                    ->grow(false),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label('Days Since Created')
                                    ->getStateUsing(fn ($record) => $record->created_at->diffInDays().' days ago')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}