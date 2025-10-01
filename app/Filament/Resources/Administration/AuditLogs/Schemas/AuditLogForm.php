<?php

namespace App\Filament\Resources\Administration\AuditLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('model_type'),
                TextInput::make('model_id')
                    ->numeric(),
                Select::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                KeyValue::make('old_values'),
                KeyValue::make('new_values'),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('user_type'),
                TextInput::make('ip_address'),
                Textarea::make('user_agent'),
                TextInput::make('url'),
                TextInput::make('method'),
                KeyValue::make('request_data'),
                TextInput::make('batch_uuid'),
                TagsInput::make('tags'),
                DateTimePicker::make('occurred_at'),
            ]);
    }
}
