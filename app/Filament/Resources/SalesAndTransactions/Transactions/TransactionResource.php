<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions;

use App\Filament\Resources\SalesAndTransactions\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\SalesAndTransactions\Transactions\Pages\EditTransaction;
use App\Filament\Resources\SalesAndTransactions\Transactions\Pages\ListTransactions;
use App\Filament\Resources\SalesAndTransactions\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\SalesAndTransactions\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\SalesAndTransactions\Transactions\Schemas\TransactionInfolist;
use App\Filament\Resources\SalesAndTransactions\Transactions\Tables\TransactionsTable;
use App\Models\Transaction\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view' => ViewTransaction::route('/{record}'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
