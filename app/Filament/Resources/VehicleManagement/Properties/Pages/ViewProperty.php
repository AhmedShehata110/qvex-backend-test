<?php

namespace App\Filament\Resources\VehicleManagement\Properties\Pages;

use App\Filament\Resources\VehicleManagement\Properties\PropertyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}