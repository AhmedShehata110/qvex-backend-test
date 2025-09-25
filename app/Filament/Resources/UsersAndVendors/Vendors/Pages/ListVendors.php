<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Pages;

use App\Filament\Resources\UsersAndVendors\Vendors\VendorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
