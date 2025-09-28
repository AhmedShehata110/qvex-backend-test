<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Schemas;

use App\Models\Vehicle\VehicleModel;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;

class CategoriesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Vehicle Model Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('make.name')
                                    ->label('Vehicle Make')
                                    ->weight('bold')
                                    ->color('primary'),

                                TextEntry::make('name')
                                    ->label('Model Name')
                                    ->weight('bold')
                                    ->size(TextSize::Large),

                                TextEntry::make('name_ar')
                                    ->label('Arabic Name')
                                    ->placeholder('Not specified'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('slug')
                                    ->label('URL Slug')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('body_type')
                                    ->label('Body Type')
                                    ->formatStateUsing(fn (string $state): string => VehicleModel::getBodyTypes()[$state] ?? ucfirst($state))
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match ($state) {
                                            VehicleModel::BODY_TYPE_SEDAN, VehicleModel::BODY_TYPE_COUPE => 'primary',
                                            VehicleModel::BODY_TYPE_SUV, VehicleModel::BODY_TYPE_PICKUP => 'success',
                                            VehicleModel::BODY_TYPE_HATCHBACK, VehicleModel::BODY_TYPE_WAGON => 'warning',
                                            VehicleModel::BODY_TYPE_CONVERTIBLE, VehicleModel::BODY_TYPE_VAN => 'info',
                                            VehicleModel::BODY_TYPE_TRUCK, VehicleModel::BODY_TYPE_MOTORCYCLE => 'danger',
                                            default => 'gray'
                                        };
                                    }),

                                IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(IconSize::Large),
                            ]),
                    ]),

                Section::make('Production Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('year_start')
                                    ->label('Production Start Year')
                                    ->badge()
                                    ->color('info')
                                    ->size(TextSize::Large),

                                TextEntry::make('year_end')
                                    ->label('Production End Year')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('Still in production')
                                    ->size(TextSize::Large),

                                TextEntry::make('year_range')
                                    ->label('Production Period')
                                    ->badge()
                                    ->color(fn ($record) => $record->isCurrent() ? 'success' : 'gray')
                                    ->size(TextSize::Large),
                            ]),

                        TextEntry::make('current_production_status')
                            ->label('Current Production Status')
                            ->state(fn ($record): string => $record->isCurrent() ? 'Currently in production' : 'Production discontinued')
                            ->badge()
                            ->color(fn ($record) => $record->isCurrent() ? 'success' : 'danger'),
                    ]),

                Section::make('Vehicle Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('vehicle_count')
                                    ->label('Total Vehicles')
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large),

                                TextEntry::make('active_vehicle_count')
                                    ->label('Active Vehicles')
                                    ->badge()
                                    ->color('success')
                                    ->size(TextSize::Large),

                                TextEntry::make('sort_order')
                                    ->label('Sort Order')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('full_name')
                                    ->label('Full Name')
                                    ->weight('bold')
                                    ->copyable()
                                    ->copyMessage('Full name copied!'),
                            ]),
                    ]),

                Group::make()
                    ->schema([
                        Section::make('System Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('addedBy.name')
                                            ->label('Added By')
                                            ->placeholder('System'),

                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->dateTime(),

                                        TextEntry::make('deleted_at')
                                            ->label('Deleted At')
                                            ->dateTime()
                                            ->placeholder('Not deleted')
                                            ->visible(fn ($record) => $record->trashed()),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(3);
    }
}
