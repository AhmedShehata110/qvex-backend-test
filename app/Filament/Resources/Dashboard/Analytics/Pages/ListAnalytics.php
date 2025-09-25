<?php

namespace App\Filament\Resources\Dashboard\Analytics\Pages;

use App\Filament\Resources\Dashboard\Analytics\AnalyticsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
