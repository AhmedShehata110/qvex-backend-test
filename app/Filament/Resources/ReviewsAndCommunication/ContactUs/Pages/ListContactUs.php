<?php

namespace App\Filament\Resources\ReviewsAndCommunication\ContactUs\Pages;

use App\Filament\Resources\ReviewsAndCommunication\ContactUs\ContactUsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactUs extends ListRecords
{
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
