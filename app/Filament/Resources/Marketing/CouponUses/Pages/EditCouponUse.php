<?php

namespace App\Filament\Resources\Marketing\CouponUses\Pages;

use App\Filament\Resources\Marketing\CouponUses\CouponUseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCouponUse extends EditRecord
{
    protected static string $resource = CouponUseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
