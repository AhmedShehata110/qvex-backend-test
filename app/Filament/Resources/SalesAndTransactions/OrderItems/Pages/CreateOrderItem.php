<?php

namespace App\Filament\Resources\SalesAndTransactions\OrderItems\Pages;

use App\Filament\Resources\SalesAndTransactions\OrderItems\OrderItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;
}
