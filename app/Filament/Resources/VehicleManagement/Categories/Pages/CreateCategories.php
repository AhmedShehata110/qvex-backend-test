<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Pages;

use App\Filament\Resources\VehicleManagement\Categories\CategoriesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategories extends CreateRecord
{
    protected static string $resource = CategoriesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Vehicle model created successfully';
    }
}
