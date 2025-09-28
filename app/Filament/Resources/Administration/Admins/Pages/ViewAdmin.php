<?php

namespace App\Filament\Resources\Administration\Admins\Pages;

use App\Filament\Resources\Administration\Admins\AdminResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmin extends ViewRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}