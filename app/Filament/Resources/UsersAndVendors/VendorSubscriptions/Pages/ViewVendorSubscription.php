<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages;

use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\VendorSubscriptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorSubscription extends ViewRecord
{
    protected static string $resource = VendorSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
