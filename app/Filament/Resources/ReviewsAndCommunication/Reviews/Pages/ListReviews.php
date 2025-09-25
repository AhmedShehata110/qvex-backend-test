<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Reviews\ReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
