<?php

namespace App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages;

use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;
}
