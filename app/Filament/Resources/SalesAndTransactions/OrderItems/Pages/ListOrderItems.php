<?php

namespace App\Filament\Resources\SalesAndTransactions\OrderItems\Pages;

use App\Filament\Resources\SalesAndTransactions\OrderItems\OrderItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderItems extends ListRecords
{
    protected static string $resource = OrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
