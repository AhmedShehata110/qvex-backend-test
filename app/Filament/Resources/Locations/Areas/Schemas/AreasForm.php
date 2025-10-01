<?php

namespace App\Filament\Resources\Locations\Areas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AreasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                Textarea::make('description'),
                TextInput::make('postal_code'),
                Toggle::make('is_active'),
            ]);
    }
}
