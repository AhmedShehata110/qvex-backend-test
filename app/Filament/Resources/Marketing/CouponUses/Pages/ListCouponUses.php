<?php

namespace App\Filament\Resources\Marketing\CouponUses\Pages;

use App\Filament\Resources\Marketing\CouponUses\CouponUseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCouponUses extends ListRecords
{
    protected static string $resource = CouponUseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
