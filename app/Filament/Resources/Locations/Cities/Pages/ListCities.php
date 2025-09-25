<?php

namespace App\Filament\Resources\Locations\Cities\Pages;

use App\Filament\Resources\Locations\Cities\CitiesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCities extends ListRecords
{
    protected static string $resource = CitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
