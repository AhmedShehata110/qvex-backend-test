<?php

namespace App\Filament\Resources\VehicleManagement\Vehicles;

use App\Filament\Resources\VehicleManagement\Vehicles\Pages\CreateVehicle;
use App\Filament\Resources\VehicleManagement\Vehicles\Pages\EditVehicle;
use App\Filament\Resources\VehicleManagement\Vehicles\Pages\ListVehicles;
use App\Filament\Resources\VehicleManagement\Vehicles\Pages\ViewVehicle;
use App\Filament\Resources\VehicleManagement\Vehicles\Schemas\VehicleForm;
use App\Filament\Resources\VehicleManagement\Vehicles\Schemas\VehicleInfolist;
use App\Filament\Resources\VehicleManagement\Vehicles\Tables\VehiclesTable;
use App\Models\Vehicle\Vehicle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|UnitEnum|null $navigationGroup = 'Vehicle Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.vehicles');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.vehicle_management');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table);
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
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'view' => ViewVehicle::route('/{record}'),
            'edit' => EditVehicle::route('/{record}/edit'),
        ];
    }
}
