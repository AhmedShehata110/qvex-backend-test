<?php

namespace App\Filament\Resources\VehicleManagement\Properties;

use App\Filament\Resources\VehicleManagement\Properties\Pages\CreateProperty;
use App\Filament\Resources\VehicleManagement\Properties\Pages\EditProperty;
use App\Filament\Resources\VehicleManagement\Properties\Pages\ListProperties;
use App\Filament\Resources\VehicleManagement\Properties\Pages\ViewProperty;
use App\Filament\Resources\VehicleManagement\Properties\Schemas\PropertyForm;
use App\Filament\Resources\VehicleManagement\Properties\Schemas\PropertyInfolist;
use App\Filament\Resources\VehicleManagement\Properties\Tables\PropertiesTable;
use App\Models\Vehicle\VehicleFeature; // Using existing model
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PropertyResource extends Resource
{
    protected static ?string $model = VehicleFeature::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Vehicle Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Properties & Features';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PropertyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PropertyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertiesTable::configure($table);
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
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'view' => ViewProperty::route('/{record}'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }
}
