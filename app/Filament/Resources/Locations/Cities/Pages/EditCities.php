<?php

namespace App\Filament\Resources\Locations\Cities\Pages;

use App\Filament\Resources\Locations\Cities\CitiesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCities extends EditRecord
{
    protected static string $resource = CitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
