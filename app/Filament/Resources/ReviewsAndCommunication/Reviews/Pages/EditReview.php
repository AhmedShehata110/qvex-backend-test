<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Reviews\ReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
