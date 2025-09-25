<?php

namespace App\Filament\Resources\SalesAndTransactions\OrderItems;

use App\Filament\Resources\SalesAndTransactions\OrderItems\Pages\CreateOrderItem;
use App\Filament\Resources\SalesAndTransactions\OrderItems\Pages\EditOrderItem;
use App\Filament\Resources\SalesAndTransactions\OrderItems\Pages\ListOrderItems;
use App\Filament\Resources\SalesAndTransactions\OrderItems\Schemas\OrderItemForm;
use App\Filament\Resources\SalesAndTransactions\OrderItems\Tables\OrderItemsTable;
use App\Models\SalesAndTransactions\OrderItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrderItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderItemsTable::configure($table);
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
            'index' => ListOrderItems::route('/'),
            'create' => CreateOrderItem::route('/create'),
            'edit' => EditOrderItem::route('/{record}/edit'),
        ];
    }
}
