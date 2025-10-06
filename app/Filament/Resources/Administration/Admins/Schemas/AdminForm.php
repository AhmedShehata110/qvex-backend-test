<?php

namespace App\Filament\Resources\Administration\Admins\Schemas;

use App\Enums\User\UserTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('keys.email_address'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->confirmed()
                    ->revealable(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->label(__('keys.confirm_password')),
                TextInput::make('locale')
                    ->required()
                    ->default('en')
                    ->maxLength(5),
                TextInput::make('timezone')
                    ->required()
                    ->default('UTC')
                    ->maxLength(50),
                DatePicker::make('birth_date'),
                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other'
                    ]),
                Toggle::make('is_active')
                    ->label(__('keys.active'))
                    ->default(true),
                Toggle::make('two_factor_enabled')
                    ->label(__('keys.two_factor_authentication'))
                    ->default(false),
            ]);
    }
}
