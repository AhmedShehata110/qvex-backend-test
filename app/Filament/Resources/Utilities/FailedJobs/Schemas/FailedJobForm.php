<?php

namespace App\Filament\Resources\Utilities\FailedJobs\Schemas;

use Filament\Schemas\Schema;

class FailedJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
