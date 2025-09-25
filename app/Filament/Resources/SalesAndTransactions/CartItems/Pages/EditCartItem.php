<?php

namespace App\Filament\Resources\SalesAndTransactions\CartItems\Pages;

use App\Filament\Resources\SalesAndTransactions\CartItems\CartItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCartItem extends EditRecord
{
    protected static string $resource = CartItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
