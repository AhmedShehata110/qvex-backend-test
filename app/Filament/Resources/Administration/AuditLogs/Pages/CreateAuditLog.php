<?php

namespace App\Filament\Resources\Administration\AuditLogs\Pages;

use App\Filament\Resources\Administration\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditLog extends CreateRecord
{
    protected static string $resource = AuditLogResource::class;
}
