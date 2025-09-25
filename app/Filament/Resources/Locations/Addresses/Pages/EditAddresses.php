<?php

namespace App\Filament\Resources\Locations\Addresses\Pages;

use App\Filament\Resources\Locations\Addresses\AddressesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAddresses extends EditRecord
{
    protected static string $resource = AddressesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
