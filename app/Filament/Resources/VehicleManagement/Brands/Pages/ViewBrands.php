<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Pages;

use App\Filament\Resources\VehicleManagement\Brands\BrandsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBrands extends ViewRecord
{
    protected static string $resource = BrandsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Brand: '.$this->record->name;
    }
}
