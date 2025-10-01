<?php

namespace App\Filament\Resources\Locations\Branches\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description'),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                Textarea::make('address')
                    ->required(),
                TextInput::make('phone'),
                TextInput::make('email')
                    ->email(),
                TextInput::make('manager_name'),
                TextInput::make('latitude'),
                TextInput::make('longitude'),
                KeyValue::make('working_hours'),
                Toggle::make('is_active'),
            ]);
    }
}
