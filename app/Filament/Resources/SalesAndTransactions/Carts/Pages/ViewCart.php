<?php

namespace App\Filament\Resources\SalesAndTransactions\Carts\Pages;

use App\Filament\Resources\SalesAndTransactions\Carts\CartResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
