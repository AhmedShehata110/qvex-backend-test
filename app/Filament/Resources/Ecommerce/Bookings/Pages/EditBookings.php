<?php

namespace App\Filament\Resources\Ecommerce\Bookings\Pages;

use App\Filament\Resources\Ecommerce\Bookings\BookingsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookings extends EditRecord
{
    protected static string $resource = BookingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
