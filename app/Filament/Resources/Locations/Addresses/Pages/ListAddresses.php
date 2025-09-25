<?php

namespace App\Filament\Resources\Locations\Addresses\Pages;

use App\Filament\Resources\Locations\Addresses\AddressesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAddresses extends ListRecords
{
    protected static string $resource = AddressesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
