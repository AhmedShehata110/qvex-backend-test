<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reviewer.name'),
                TextEntry::make('reviewee.name'),
                TextEntry::make('transaction.transaction_number'),
                TextEntry::make('vehicle.name'),
                TextEntry::make('vendor.name'),
                TextEntry::make('rating'),
                TextEntry::make('title'),
                TextEntry::make('content'),
                TextEntry::make('pros'),
                TextEntry::make('cons'),
                TextEntry::make('would_recommend'),
                TextEntry::make('verified_purchase'),
                TextEntry::make('status'),
            ]);
    }
}
