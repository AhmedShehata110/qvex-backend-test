<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Pages;

use App\Filament\Resources\VehicleManagement\Colors\ColorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditColor extends EditRecord
{
    protected static string $resource = ColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
