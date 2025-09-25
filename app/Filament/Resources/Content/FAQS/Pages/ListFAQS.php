<?php

namespace App\Filament\Resources\Content\FAQS\Pages;

use App\Filament\Resources\Content\FAQS\FAQResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFAQS extends ListRecords
{
    protected static string $resource = FAQResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
