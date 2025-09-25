<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Pages;

use App\Filament\Resources\SalesAndTransactions\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
