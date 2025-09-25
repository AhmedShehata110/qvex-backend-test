<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages;

use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\VendorSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorSubscription extends EditRecord
{
    protected static string $resource = VendorSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
