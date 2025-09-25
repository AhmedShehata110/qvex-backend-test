<?php

namespace App\Filament\Resources\Locations\Areas\Pages;

use App\Filament\Resources\Locations\Areas\AreasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAreas extends ListRecords
{
    protected static string $resource = AreasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
