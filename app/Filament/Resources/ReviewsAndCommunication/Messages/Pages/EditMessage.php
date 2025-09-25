<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Messages\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Messages\MessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMessage extends EditRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
