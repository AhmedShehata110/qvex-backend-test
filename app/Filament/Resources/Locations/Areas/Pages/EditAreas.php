<?php

namespace App\Filament\Resources\Locations\Areas\Pages;

use App\Filament\Resources\Locations\Areas\AreasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAreas extends EditRecord
{
    protected static string $resource = AreasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
