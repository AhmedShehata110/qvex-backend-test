<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class ColorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Color Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('keys.color_name'))
                                    ->size(TextSize::Large)
                                    ->weight('bold')
                                    ->color('primary'),

                                ColorEntry::make('hex_code')
                                    ->label(__('keys.color_preview')),

                                TextEntry::make('hex_code')
                                    ->label(__('keys.hex_code'))
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('rgb_value')
                                    ->label(__('keys.rgb_value'))
                                    ->copyable()
                                    ->placeholder(__('keys.not_provided')),

                                TextEntry::make('type')
                                    ->label(__('keys.type'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'exterior' => 'success',
                                        'interior' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                                Grid::make(3)
                                    ->schema([
                                        IconEntry::make('is_metallic')
                                            ->label(__('keys.metallic'))
                                            ->boolean()
                                            ->trueIcon('heroicon-o-sparkles')
                                            ->falseIcon('heroicon-o-minus')
                                            ->trueColor('warning')
                                            ->falseColor('gray'),

                                        IconEntry::make('is_popular')
                                            ->label(__('keys.popular'))
                                            ->boolean()
                                            ->trueIcon('heroicon-o-star')
                                            ->falseIcon('heroicon-o-minus')
                                            ->trueColor('warning')
                                            ->falseColor('gray'),

                                        TextEntry::make('sort_order')
                                            ->label(__('keys.sort_order'))
                                            ->badge()
                                            ->color('secondary'),
                                    ]),
                            ]),

                        Section::make('Color Statistics')
                            ->schema([
                                TextEntry::make('vehicles_count')
                                    ->label(__('keys.total_vehicles'))
                                    ->getStateUsing(fn ($record) => $record->vehicles()->count())
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large),

                                TextEntry::make('active_vehicles_count')
                                    ->label(__('keys.active_vehicles'))
                                    ->getStateUsing(fn ($record) => $record->activeVehicles()->count())
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('keys.created_at'))
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label(__('keys.updated_at'))
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label(__('keys.days_since_created'))
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
