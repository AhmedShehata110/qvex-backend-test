<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages;

use App\Filament\Resources\ReviewsAndCommunication\Reviews\ReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}
