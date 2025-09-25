<?php

namespace App\Filament\Resources\Content\EmailTemplates\Pages;

use App\Filament\Resources\Content\EmailTemplates\EmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;
}
