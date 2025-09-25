<?php

namespace App\Filament\Resources\Communication\Newsletters\Pages;

use App\Filament\Resources\Communication\Newsletters\NewsletterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletter extends CreateRecord
{
    protected static string $resource = NewsletterResource::class;
}
