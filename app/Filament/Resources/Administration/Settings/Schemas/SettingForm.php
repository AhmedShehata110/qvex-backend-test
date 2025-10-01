<?php

namespace App\Filament\Resources\Administration\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('value'),
                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                        'text' => 'Text',
                    ])
                    ->required(),
                TextInput::make('group'),
                Textarea::make('description'),
                Toggle::make('is_public'),
            ]);
    }
}
