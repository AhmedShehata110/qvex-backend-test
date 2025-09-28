<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Pages;

use App\Filament\Resources\SalesAndTransactions\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}