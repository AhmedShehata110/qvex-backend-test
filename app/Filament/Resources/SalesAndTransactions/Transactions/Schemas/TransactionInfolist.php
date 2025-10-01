<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('transaction_number'),
                TextEntry::make('buyer.name'),
                TextEntry::make('seller.name'),
                TextEntry::make('vehicle.name'),
                TextEntry::make('type'),
                TextEntry::make('status'),
                TextEntry::make('subtotal')->money('USD'),
                TextEntry::make('tax_amount')->money('USD'),
                TextEntry::make('commission_amount')->money('USD'),
                TextEntry::make('total_amount')->money('USD'),
                TextEntry::make('paid_amount')->money('USD'),
                TextEntry::make('refunded_amount')->money('USD'),
                TextEntry::make('currency'),
                KeyValueEntry::make('transaction_data'),
                TextEntry::make('notes'),
                TextEntry::make('cancellation_reason'),
                TextEntry::make('confirmed_at')->dateTime(),
                TextEntry::make('completed_at')->dateTime(),
                TextEntry::make('cancelled_at')->dateTime(),
            ]);
    }
}
