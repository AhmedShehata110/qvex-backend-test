<?php

namespace App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('description')
                    ->required(),
                Select::make('category')
                    ->options([
                        'general' => 'General',
                        'technical' => 'Technical',
                        'billing' => 'Billing',
                    ]),
                Select::make('priority')
                    ->options([
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
                        4 => 'Urgent',
                    ]),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                TextInput::make('assigned_to')
                    ->numeric(),
                DateTimePicker::make('last_reply_at'),
                DateTimePicker::make('resolved_at'),
                Textarea::make('resolution_notes'),
                TagsInput::make('tags'),
                KeyValue::make('metadata'),
            ]);
    }
}
