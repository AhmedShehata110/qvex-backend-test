<?php

namespace App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages;

use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\SupportTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupportTicket extends EditRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
