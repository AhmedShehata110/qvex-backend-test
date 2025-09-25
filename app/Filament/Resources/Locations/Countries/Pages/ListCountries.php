<?php

namespace App\Filament\Resources\Locations\Countries\Pages;

use App\Filament\Resources\Locations\Countries\CountriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    protected static string $resource = CountriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
