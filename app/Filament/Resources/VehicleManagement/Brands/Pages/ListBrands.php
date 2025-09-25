<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Pages;

use App\Filament\Resources\VehicleManagement\Brands\BrandsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Brand')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Vehicle Brands';
    }
}
