<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Pages;

use App\Filament\Resources\SalesAndTransactions\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
