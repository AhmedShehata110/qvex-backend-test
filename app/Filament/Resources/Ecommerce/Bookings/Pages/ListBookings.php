<?php

namespace App\Filament\Resources\Ecommerce\Bookings\Pages;

use App\Filament\Resources\Ecommerce\Bookings\BookingsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
