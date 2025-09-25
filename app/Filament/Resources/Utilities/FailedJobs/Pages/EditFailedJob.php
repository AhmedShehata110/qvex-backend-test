<?php

namespace App\Filament\Resources\Utilities\FailedJobs\Pages;

use App\Filament\Resources\Utilities\FailedJobs\FailedJobResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFailedJob extends EditRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
