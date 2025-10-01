<?php

namespace App\Filament\Resources\ReviewsAndCommunication\ContactUs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactUsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone'),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('message')
                    ->required(),
                Select::make('category')
                    ->options([
                        'general' => 'General',
                        'support' => 'Support',
                        'feedback' => 'Feedback',
                    ]),
                Select::make('priority')
                    ->options([
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
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
                Textarea::make('response'),
                DateTimePicker::make('responded_at'),
                TextInput::make('responded_by')
                    ->numeric(),
                KeyValue::make('metadata'),
            ]);
    }
}
