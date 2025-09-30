<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Pages;

use App\Filament\Resources\UsersAndVendors\VendorStaff\VendorStaffResource;
use Filament\Resources\Pages\EditRecord;

class EditVendorStaff extends EditRecord
{
    protected static string $resource = VendorStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions here
        ];
    }
}
