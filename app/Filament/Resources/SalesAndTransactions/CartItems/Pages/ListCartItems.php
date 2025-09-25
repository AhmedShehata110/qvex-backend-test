<?php

namespace App\Filament\Resources\SalesAndTransactions\CartItems\Pages;

use App\Filament\Resources\SalesAndTransactions\CartItems\CartItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCartItems extends ListRecords
{
    protected static string $resource = CartItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
