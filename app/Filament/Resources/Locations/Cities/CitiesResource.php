<?php

namespace App\Filament\Resources\Locations\Cities;

use App\Filament\Resources\Locations\Cities\Pages\CreateCities;
use App\Filament\Resources\Locations\Cities\Pages\EditCities;
use App\Filament\Resources\Locations\Cities\Pages\ListCities;
use App\Filament\Resources\Locations\Cities\Schemas\CitiesForm;
use App\Filament\Resources\Locations\Cities\Tables\CitiesTable;
use App\Models\Location\City;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CitiesResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CitiesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
            'create' => CreateCities::route('/create'),
            'edit' => EditCities::route('/{record}/edit'),
        ];
    }
}
