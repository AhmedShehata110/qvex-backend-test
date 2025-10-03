<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Messages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sender_id')
                    ->numeric(),
                TextInput::make('recipient_id')
                    ->numeric(),
                TextInput::make('vehicle_id')
                    ->numeric(),
                TextInput::make('vendor_id')
                    ->numeric(),
                TextInput::make('conversation_id')
                    ->numeric(),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('body')
                    ->required(),
                Select::make('message_type')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'in_app' => 'In App',
                    ]),
                Select::make('priority')
                    ->options([
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
                    ]),
                Select::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'read' => 'Read',
                        'failed' => 'Failed',
                    ]),
                SpatieMediaLibraryFileUpload::make('attachments'),
                KeyValue::make('metadata'),
                Toggle::make('is_read'),
                Toggle::make('is_starred'),
                Toggle::make('is_archived'),
                DateTimePicker::make('read_at'),
                DateTimePicker::make('replied_at'),
                DateTimePicker::make('scheduled_at'),
                Textarea::make('auto_response'),
                TextInput::make('template_used'),
                TextInput::make('source'),
            ]);
    }
}
