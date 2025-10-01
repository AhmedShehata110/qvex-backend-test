<?php

namespace App\Filament\Resources\Content\EmailTemplates\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->options([
                        'welcome' => 'Welcome',
                        'marketing' => 'Marketing',
                        'transaction' => 'Transaction',
                        'notification' => 'Notification',
                    ]),
                Select::make('type')
                    ->options([
                        'html' => 'HTML',
                        'text' => 'Text',
                        'both' => 'Both',
                    ]),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('html_content'),
                Textarea::make('text_content'),
                KeyValue::make('variables'),
                Textarea::make('description'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                Toggle::make('is_default'),
                KeyValue::make('preview_data'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('sender_name'),
                TextInput::make('sender_email')
                    ->email(),
                TextInput::make('reply_to')
                    ->email(),
                KeyValue::make('design_json'),
                TextInput::make('usage_count')
                    ->numeric(),
            ]);
    }
}
