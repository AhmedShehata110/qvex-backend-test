<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Pages;

use App\Filament\Resources\UsersAndVendors\Vendors\VendorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
