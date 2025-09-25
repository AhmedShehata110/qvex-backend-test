<?php

namespace App\Filament\Resources\SalesAndTransactions\CartItems\Pages;

use App\Filament\Resources\SalesAndTransactions\CartItems\CartItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCartItem extends CreateRecord
{
    protected static string $resource = CartItemResource::class;
}
