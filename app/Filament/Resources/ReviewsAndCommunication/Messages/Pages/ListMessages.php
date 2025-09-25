<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Messages\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Messages\MessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
