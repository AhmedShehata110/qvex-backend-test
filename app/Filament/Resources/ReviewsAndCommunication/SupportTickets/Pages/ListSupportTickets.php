<?php

namespace App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages;

use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\SupportTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
