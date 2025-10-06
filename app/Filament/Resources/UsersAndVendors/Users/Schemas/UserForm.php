<?php

namespace App\Filament\Resources\UsersAndVendors\Users\Schemas;

use App\Enums\User\UserTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label(__('keys.email_address'))
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                DateTimePicker::make('email_verified_at'),
                DateTimePicker::make('phone_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('locale')
                    ->required()
                    ->default('en'),
                TextInput::make('timezone')
                    ->required()
                    ->default('UTC'),
                TextInput::make('avatar'),
                DatePicker::make('birth_date'),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                Select::make('user_type')
                    ->options(UserTypeEnum::class)
                    ->default('user')
                    ->required(),
                DateTimePicker::make('last_login_at'),
                TextInput::make('last_login_ip'),
                Toggle::make('two_factor_enabled')
                    ->required(),
                TextInput::make('two_factor_secret'),
                TextInput::make('two_factor_recovery_codes'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('added_by_id')
                    ->numeric(),
            ]);
    }
}
