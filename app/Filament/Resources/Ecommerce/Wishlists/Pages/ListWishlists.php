<?php

namespace App\Filament\Resources\Ecommerce\Wishlists\Pages;

use App\Filament\Resources\Ecommerce\Wishlists\WishlistsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWishlists extends ListRecords
{
    protected static string $resource = WishlistsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
