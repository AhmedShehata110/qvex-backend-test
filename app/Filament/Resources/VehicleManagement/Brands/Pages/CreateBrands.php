<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Pages;

use App\Filament\Resources\VehicleManagement\Brands\BrandsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBrands extends CreateRecord
{
    protected static string $resource = BrandsResource::class;

    public function getTitle(): string
    {
        return 'Create New Brand';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
