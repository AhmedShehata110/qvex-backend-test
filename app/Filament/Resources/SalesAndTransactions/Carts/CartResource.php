<?php

namespace App\Filament\Resources\SalesAndTransactions\Carts;

use App\Filament\Resources\SalesAndTransactions\Carts\Pages\CreateCart;
use App\Filament\Resources\SalesAndTransactions\Carts\Pages\EditCart;
use App\Filament\Resources\SalesAndTransactions\Carts\Pages\ListCarts;
use App\Filament\Resources\SalesAndTransactions\Carts\Pages\ViewCart;
use App\Filament\Resources\SalesAndTransactions\Carts\Schemas\CartForm;
use App\Filament\Resources\SalesAndTransactions\Carts\Tables\CartsTable;
use App\Models\SalesAndTransactions\Cart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return CartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartsTable::configure($table);
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
            'index' => ListCarts::route('/'),
            'create' => CreateCart::route('/create'),
            'view' => ViewCart::route('/{record}'),
            'edit' => EditCart::route('/{record}/edit'),
        ];
    }
}
