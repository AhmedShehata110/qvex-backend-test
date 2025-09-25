<?php

namespace App\Filament\Resources\Administration\AuditLogs\Pages;

use App\Filament\Resources\Administration\AuditLogs\AuditLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAuditLog extends EditRecord
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
