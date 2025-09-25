<?php

namespace App\Filament\Resources\Ecommerce\Wishlists\Pages;

use App\Filament\Resources\Ecommerce\Wishlists\WishlistsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWishlists extends EditRecord
{
    protected static string $resource = WishlistsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
