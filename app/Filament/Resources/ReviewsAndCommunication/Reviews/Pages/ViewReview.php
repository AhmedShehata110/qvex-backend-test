<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Reviews\ReviewResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
