<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Pages;

use App\Filament\Resources\VehicleManagement\Categories\CategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCategories extends ViewRecord
{
    protected static string $resource = CategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
