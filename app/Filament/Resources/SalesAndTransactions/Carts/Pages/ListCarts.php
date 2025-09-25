<?php

namespace App\Filament\Resources\SalesAndTransactions\Carts\Pages;

use App\Filament\Resources\SalesAndTransactions\Carts\CartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
