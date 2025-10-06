<?php

namespace App\Filament\Resources\Ecommerce\Products;

use App\Filament\Resources\Ecommerce\Products\Pages\CreateProducts;
use App\Filament\Resources\Ecommerce\Products\Pages\EditProducts;
use App\Filament\Resources\Ecommerce\Products\Pages\ListProducts;
use App\Filament\Resources\Ecommerce\Products\Schemas\ProductsForm;
use App\Filament\Resources\Ecommerce\Products\Tables\ProductsTable;
use App\Models\Vehicle\Vehicle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductsResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('keys.products_vehicles');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.ecommerce');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProductsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProducts::route('/create'),
            'edit' => EditProducts::route('/{record}/edit'),
        ];
    }
}
