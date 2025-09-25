<?php

namespace App\Filament\Resources\Locations\Branches\Pages;

use App\Filament\Resources\Locations\Branches\BranchesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBranches extends EditRecord
{
    protected static string $resource = BranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
