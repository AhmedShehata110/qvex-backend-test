<?php

namespace App\Filament\Resources\VehicleManagement\Vehicles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                            Section::make('Vehicle Information')
                                ->schema([
                                    TextEntry::make('title')
                                        ->label('Vehicle Title')
                                        ->size(TextSize::Large)
                                        ->weight('bold')
                                        ->color('primary'),

                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('make.name')
                                                ->label('Make')
                                                ->badge()
                                                ->color('gray'),

                                            TextEntry::make('model.name')
                                                ->label('Model')
                                                ->badge()
                                                ->color('primary'),

                                            TextEntry::make('year')
                                                ->label('Year')
                                                ->badge()
                                                ->color('info'),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('condition')
                                                ->label('Condition')
                                                ->badge()
                                                ->color(fn ($state) => match ($state) {
                                                    'new' => 'success',
                                                    'certified_preowned' => 'warning',
                                                    'used' => 'info',
                                                    'salvage' => 'danger',
                                                    default => 'gray'
                                                })
                                                ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),

                                            TextEntry::make('availability_type')
                                                ->label('Availability')
                                                ->badge()
                                                ->color(fn ($state) => match ($state) {
                                                    'sale' => 'primary',
                                                    'rent' => 'warning',
                                                    'both' => 'success',
                                                    default => 'gray'
                                                })
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'sale' => 'For Sale',
                                                    'rent' => 'For Rent',
                                                    'both' => 'Sale & Rent',
                                                    default => ucfirst($state)
                                                }),
                                        ]),

                                    TextEntry::make('vin')
                                        ->label('VIN Number')
                                        ->copyable()
                                        ->placeholder('Not provided'),

                                    TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull()
                                        ->placeholder('No description provided'),
                                ]),

                            Section::make('Pricing & Status')
                                ->schema([
                                    TextEntry::make('price')
                                        ->label('Current Price')
                                        ->money('USD')
                                        ->size(TextSize::Large)
                                        ->weight('bold')
                                        ->color('success'),

                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('original_price')
                                                ->label('Original Price')
                                                ->money('USD')
                                                ->placeholder('Same as current'),

                                            IconEntry::make('is_negotiable')
                                                ->label('Negotiable')
                                                ->boolean()
                                                ->trueIcon('heroicon-o-check')
                                                ->falseIcon('heroicon-o-x-mark')
                                                ->trueColor('success')
                                                ->falseColor('danger'),
                                        ]),

                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn ($state) => match ($state) {
                                            'active' => 'success',
                                            'pending_approval' => 'warning',
                                            'sold' => 'danger',
                                            'rented' => 'info',
                                            'inactive' => 'gray',
                                            'draft' => 'gray',
                                            default => 'gray'
                                        })
                                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),

                                    Grid::make(3)
                                        ->schema([
                                            IconEntry::make('is_featured')
                                                ->label('Featured')
                                                ->boolean()
                                                ->trueIcon('heroicon-s-star')
                                                ->falseIcon('heroicon-o-star')
                                                ->trueColor('warning')
                                                ->falseColor('gray'),

                                            IconEntry::make('is_urgent')
                                                ->label('Urgent Sale')
                                                ->boolean()
                                                ->trueIcon('heroicon-o-clock')
                                                ->falseIcon('heroicon-o-check')
                                                ->trueColor('danger')
                                                ->falseColor('success'),

                                            IconEntry::make('is_active')
                                                ->label('Active')
                                                ->boolean()
                                                ->trueIcon('heroicon-o-check-circle')
                                                ->falseIcon('heroicon-o-x-circle')
                                                ->trueColor('success')
                                                ->falseColor('danger'),
                                        ]),
                        ]),
                ])->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        Section::make('Technical Specifications')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('mileage')
                                            ->label('Mileage')
                                            ->formatStateUsing(fn ($state) => $state ? number_format($state).' km' : 'N/A')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('doors')
                                            ->label('Doors')
                                            ->suffix(' doors')
                                            ->placeholder('Not specified'),

                                        TextEntry::make('cylinders')
                                            ->label('Cylinders')
                                            ->suffix(' cyl')
                                            ->placeholder('Not specified'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('exterior_color')
                                            ->label('Exterior Color')
                                            ->badge()
                                            ->color('primary')
                                            ->placeholder('Not specified'),

                                        TextEntry::make('interior_color')
                                            ->label('Interior Color')
                                            ->badge()
                                            ->color('warning')
                                            ->placeholder('Not specified'),
                                    ]),

                                TextEntry::make('license_plate')
                                    ->label('License Plate')
                                    ->copyable()
                                    ->placeholder('Not provided'),
                            ]),

                        Section::make('Business Information')
                            ->schema([
                                TextEntry::make('vendor.name')
                                    ->label('Vendor')
                                    ->weight('bold')
                                    ->color('primary'),

                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('view_count')
                                            ->label('Views')
                                            ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('inquiry_count')
                                            ->label('Inquiries')
                                            ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                                            ->badge()
                                            ->color('warning'),

                                        TextEntry::make('favorite_count')
                                            ->label('Favorites')
                                            ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                                            ->badge()
                                            ->color('danger'),
                                    ]),

                                TextEntry::make('location')
                                    ->label('Location')
                                    ->state(fn ($record) => collect([$record->city, $record->state, $record->country])->filter()->implode(', '))
                                    ->placeholder('Location not specified')
                                    ->icon('heroicon-m-map-pin'),
                            ]),
                    ])->columnSpanFull(),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),

                                TextEntry::make('approved_at')
                                    ->label('Approved')
                                    ->dateTime()
                                    ->placeholder('Not approved'),

                                TextEntry::make('featured_until')
                                    ->label('Featured Until')
                                    ->dateTime()
                                    ->placeholder('Not featured'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
