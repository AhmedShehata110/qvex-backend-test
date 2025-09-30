<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Pages;

use App\Filament\Resources\UsersAndVendors\VendorStaff\VendorStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorStaff extends ListRecords
{
    protected static string $resource = VendorStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
