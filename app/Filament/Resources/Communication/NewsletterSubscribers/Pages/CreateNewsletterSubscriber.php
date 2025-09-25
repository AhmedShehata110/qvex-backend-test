<?php

namespace App\Filament\Resources\Communication\NewsletterSubscribers\Pages;

use App\Filament\Resources\Communication\NewsletterSubscribers\NewsletterSubscriberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterSubscriber extends CreateRecord
{
    protected static string $resource = NewsletterSubscriberResource::class;
}
