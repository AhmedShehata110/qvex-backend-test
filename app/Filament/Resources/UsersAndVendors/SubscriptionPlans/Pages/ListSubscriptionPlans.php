<?php

namespace App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Pages;

use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\SubscriptionPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionPlans extends ListRecords
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
