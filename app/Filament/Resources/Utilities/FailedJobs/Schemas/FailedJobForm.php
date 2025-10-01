<?php

namespace App\Filament\Resources\Utilities\FailedJobs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FailedJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->disabled(),
                TextInput::make('connection'),
                TextInput::make('queue'),
                KeyValue::make('payload'),
                Textarea::make('exception')
                    ->disabled(),
                DateTimePicker::make('failed_at')
                    ->disabled(),
                DateTimePicker::make('retried_at'),
                TextInput::make('retry_count')
                    ->numeric(),
                TextInput::make('max_retries')
                    ->numeric(),
            ]);
    }
}
