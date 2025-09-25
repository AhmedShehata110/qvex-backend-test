<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Pages;

use App\Filament\Resources\VehicleManagement\Categories\CategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
