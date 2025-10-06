<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Pages;

use App\Filament\Resources\UsersAndVendors\VendorStaff\VendorStaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVendorStaff extends CreateRecord
{
    protected static string $resource = VendorStaffResource::class;

    protected function afterCreate(): void
    {
        echo "Vendor staff record created successfully!";
    }
}
