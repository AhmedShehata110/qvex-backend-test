<?php

namespace App\Filament\Resources\Locations\Branches\Pages;

use App\Filament\Resources\Locations\Branches\BranchesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
