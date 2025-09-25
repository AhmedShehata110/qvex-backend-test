<?php

namespace App\Filament\Resources\SalesAndTransactions\Orders\Pages;

use App\Filament\Resources\SalesAndTransactions\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
