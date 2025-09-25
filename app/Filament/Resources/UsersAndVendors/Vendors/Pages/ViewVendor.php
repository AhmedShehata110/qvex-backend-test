<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Pages;

use App\Filament\Resources\UsersAndVendors\Vendors\VendorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
