<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Pages;

use App\Filament\Resources\VehicleManagement\Colors\ColorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListColors extends ListRecords
{
    protected static string $resource = ColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
