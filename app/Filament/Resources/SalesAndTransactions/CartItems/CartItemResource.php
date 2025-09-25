<?php

namespace App\Filament\Resources\SalesAndTransactions\CartItems;

use App\Filament\Resources\SalesAndTransactions\CartItems\Pages\CreateCartItem;
use App\Filament\Resources\SalesAndTransactions\CartItems\Pages\EditCartItem;
use App\Filament\Resources\SalesAndTransactions\CartItems\Pages\ListCartItems;
use App\Filament\Resources\SalesAndTransactions\CartItems\Schemas\CartItemForm;
use App\Filament\Resources\SalesAndTransactions\CartItems\Tables\CartItemsTable;
use App\Models\SalesAndTransactions\CartItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CartItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartItemsTable::configure($table);
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
            'index' => ListCartItems::route('/'),
            'create' => CreateCartItem::route('/create'),
            'edit' => EditCartItem::route('/{record}/edit'),
        ];
    }
}
