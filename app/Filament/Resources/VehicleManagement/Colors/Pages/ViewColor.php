<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Pages;

use App\Filament\Resources\VehicleManagement\Colors\ColorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewColor extends ViewRecord
{
    protected static string $resource = ColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}