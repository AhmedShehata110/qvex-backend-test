<?php

namespace App\Filament\Resources\Content\FAQS\Pages;

use App\Filament\Resources\Content\FAQS\FAQResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFAQ extends ViewRecord
{
    protected static string $resource = FAQResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
