<?php

namespace App\Filament\Resources\Utilities\FailedJobs\Pages;

use App\Filament\Resources\Utilities\FailedJobs\FailedJobResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFailedJob extends CreateRecord
{
    protected static string $resource = FailedJobResource::class;
}
