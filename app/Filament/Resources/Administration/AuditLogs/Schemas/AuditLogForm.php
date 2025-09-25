<?php

namespace App\Filament\Resources\Administration\AuditLogs\Schemas;

use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
