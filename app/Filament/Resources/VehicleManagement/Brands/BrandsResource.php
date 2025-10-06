<?php

namespace App\Filament\Resources\VehicleManagement\Brands;

use App\Filament\Resources\VehicleManagement\Brands\Pages\CreateBrands;
use App\Filament\Resources\VehicleManagement\Brands\Pages\EditBrands;
use App\Filament\Resources\VehicleManagement\Brands\Pages\ListBrands;
use App\Filament\Resources\VehicleManagement\Brands\Pages\ViewBrands;
use App\Filament\Resources\VehicleManagement\Brands\Schemas\BrandsForm;
use App\Filament\Resources\VehicleManagement\Brands\Schemas\BrandsInfolist;
use App\Filament\Resources\VehicleManagement\Brands\Tables\BrandsTable;
use App\Models\Vehicle\Brand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrandsResource extends Resource
{
    protected static ?string $model = Brand::class;

    public static function getNavigationGroup(): ?string
    {
        return __('keys.vehicle_management');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('keys.brands');
    }

    protected static ?string $modelLabel = 'Brand';

    protected static ?string $pluralModelLabel = 'Brands';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BrandsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BrandsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
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
            'index' => ListBrands::route('/'),
            'create' => CreateBrands::route('/create'),
            'view' => ViewBrands::route('/{record}'),
            'edit' => EditBrands::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
