<?php

namespace App\Filament\Resources\Locations\Cities\Schemas;

use App\Models\Location\Country;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CitiesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('City Information')
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('keys.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('keys.name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('country_id')
                            ->label(__('keys.country'))
                            ->options(Country::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_active')
                            ->label(__('keys.active'))
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Section::make('Geographic Coordinates')
                    ->schema([
                        TextInput::make('latitude')
                            ->label(__('keys.latitude'))
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('e.g., 24.7136')
                            ->helperText('Decimal degrees format'),

                        TextInput::make('longitude')
                            ->label(__('keys.longitude'))
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('e.g., 46.6753')
                            ->helperText('Decimal degrees format'),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }
}
