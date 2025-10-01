<?php

namespace App\Filament\Resources\Ecommerce\Wishlists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WishlistsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('vehicle_id')
                    ->relationship('vehicle', 'name')
                    ->required(),
                Textarea::make('notes'),
            ]);
    }
}
