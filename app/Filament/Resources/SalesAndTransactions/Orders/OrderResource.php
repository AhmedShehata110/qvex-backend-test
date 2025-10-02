<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders;

use App\Filament\Resources\SalesAndTransactions\Orders\Pages\CreateOrder;
use App\Filament\Resources\SalesAndTransactions\Orders\Pages\EditOrder;
use App\Filament\Resources\SalesAndTransactions\Orders\Pages\ListOrders;
use App\Filament\Resources\SalesAndTransactions\Orders\Pages\ViewOrder;
use App\Filament\Resources\SalesAndTransactions\Orders\Schemas\OrderForm;
use App\Filament\Resources\SalesAndTransactions\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\SalesAndTransactions\Orders\Tables\OrdersTable;
use App\Models\SalesAndTransactions\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
