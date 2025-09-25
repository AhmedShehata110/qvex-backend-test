<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Split::make([
                    Grid::make(2)
                        ->schema([
                            Section::make('Vendor Information')
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Vendor Name')
                                        ->size('lg')
                                        ->weight('bold'),

                                    TextEntry::make('slug')
                                        ->label('URL Slug')
                                        ->badge()
                                        ->color('gray'),

                                    TextEntry::make('email')
                                        ->label('Email')
                                        ->icon('heroicon-m-envelope')
                                        ->copyable(),

                                    TextEntry::make('phone')
                                        ->label('Phone')
                                        ->icon('heroicon-m-phone')
                                        ->copyable(),

                                    TextEntry::make('website')
                                        ->label('Website')
                                        ->icon('heroicon-m-globe-alt')
                                        ->url(fn ($record) => $record->website)
                                        ->openUrlInNewTab()
                                        ->placeholder('No website'),

                                    TextEntry::make('address')
                                        ->label('Business Address')
                                        ->icon('heroicon-m-map-pin')
                                        ->placeholder('No address provided')
                                        ->columnSpanFull(),

                                    TextEntry::make('description')
                                        ->label('Description')
                                        ->placeholder('No description provided')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Vendor Details')
                                ->schema([
                                    TextEntry::make('owner.name')
                                        ->label('Owner')
                                        ->icon('heroicon-m-user')
                                        ->placeholder('No owner assigned'),

                                    IconEntry::make('is_active')
                                        ->label('Status')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-badge')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),

                                    TextEntry::make('vehicles_count')
                                        ->label('Total Vehicles')
                                        ->state(fn ($record) => $record->vehicles()->count())
                                        ->badge()
                                        ->color('primary'),

                                    TextEntry::make('active_vehicles_count')
                                        ->label('Active Vehicles')
                                        ->state(fn ($record) => $record->vehicles()->where('is_active', true)->count())
                                        ->badge()
                                        ->color('success'),
                                ]),
                        ]),
                ]),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),

                                TextEntry::make('deleted_at')
                                    ->label('Deleted')
                                    ->dateTime()
                                    ->placeholder('Not deleted'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
