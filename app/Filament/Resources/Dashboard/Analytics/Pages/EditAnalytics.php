<?php

namespace App\Filament\Resources\Dashboard\Analytics\Pages;

use App\Filament\Resources\Dashboard\Analytics\AnalyticsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnalytics extends EditRecord
{
    protected static string $resource = AnalyticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
