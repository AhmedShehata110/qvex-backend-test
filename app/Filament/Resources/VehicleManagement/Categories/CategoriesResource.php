<?php

namespace App\Filament\Resources\VehicleManagement\Categories;

use App\Filament\Resources\VehicleManagement\Categories\Pages\CreateCategories;
use App\Filament\Resources\VehicleManagement\Categories\Pages\EditCategories;
use App\Filament\Resources\VehicleManagement\Categories\Pages\ListCategories;
use App\Filament\Resources\VehicleManagement\Categories\Pages\ViewCategories;
use App\Filament\Resources\VehicleManagement\Categories\Schemas\CategoriesForm;
use App\Filament\Resources\VehicleManagement\Categories\Schemas\CategoriesInfolist;
use App\Filament\Resources\VehicleManagement\Categories\Tables\CategoriesTable;
use App\Models\Vehicle\VehicleModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoriesResource extends Resource
{
    protected static ?string $model = VehicleModel::class;

    public static function getNavigationGroup(): ?string
    {
        return __('keys.vehicle_management');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('keys.categories');
    }

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CategoriesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoriesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategories::route('/create'),
            'view' => ViewCategories::route('/{record}'),
            'edit' => EditCategories::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
