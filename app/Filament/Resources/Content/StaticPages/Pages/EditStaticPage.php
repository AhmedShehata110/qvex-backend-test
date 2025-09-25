<?php

namespace App\Filament\Resources\Content\StaticPages\Pages;

use App\Filament\Resources\Content\StaticPages\StaticPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
