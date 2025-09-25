<?php

namespace App\Filament\Resources\SalesAndTransactions\Transactions\Pages;

use App\Filament\Resources\SalesAndTransactions\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
