<?php

namespace App\Filament\Resources\Locations\Countries;

use App\Filament\Resources\Locations\Countries\Pages\CreateCountries;
use App\Filament\Resources\Locations\Countries\Pages\EditCountries;
use App\Filament\Resources\Locations\Countries\Pages\ListCountries;
use App\Filament\Resources\Locations\Countries\Schemas\CountriesForm;
use App\Filament\Resources\Locations\Countries\Tables\CountriesTable;
use App\Models\Location\Country;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CountriesResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.countries');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.locations');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CountriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CountriesTable::configure($table);
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
            'index' => ListCountries::route('/'),
            'create' => CreateCountries::route('/create'),
            'edit' => EditCountries::route('/{record}/edit'),
        ];
    }
}
