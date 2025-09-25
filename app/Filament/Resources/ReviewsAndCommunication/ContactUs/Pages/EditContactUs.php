<?php

namespace App\Filament\Resources\ReviewsAndCommunication\ContactUs\Pages;

use App\Filament\Resources\ReviewsAndCommunication\ContactUs\ContactUsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactUs extends EditRecord
{
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
