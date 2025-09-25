<?php

namespace App\Filament\Resources\Content\StaticPages\Pages;

use App\Filament\Resources\Content\StaticPages\StaticPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaticPage extends CreateRecord
{
    protected static string $resource = StaticPageResource::class;
}
