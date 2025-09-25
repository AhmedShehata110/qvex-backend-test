<?php

namespace App\Filament\Resources\SalesAndTransactions\Payments;

use App\Filament\Resources\SalesAndTransactions\Payments\Pages\CreatePayment;
use App\Filament\Resources\SalesAndTransactions\Payments\Pages\EditPayment;
use App\Filament\Resources\SalesAndTransactions\Payments\Pages\ListPayments;
use App\Filament\Resources\SalesAndTransactions\Payments\Schemas\PaymentForm;
use App\Filament\Resources\SalesAndTransactions\Payments\Tables\PaymentsTable;
use App\Models\Transaction\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Transactions';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $recordTitleAttribute = 'payment_reference';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
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
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
