<?php

namespace App\Filament\Resources\Communication\NewsletterSubscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                Toggle::make('is_subscribed')
                    ->label(__('keys.subscribed')),
                DateTimePicker::make('subscribed_at'),
                DateTimePicker::make('unsubscribed_at'),
                Select::make('subscription_source')
                    ->options([
                        'website' => 'Website',
                        'app' => 'App',
                        'admin' => 'Admin',
                    ]),
                KeyValue::make('preferences'),
                TextInput::make('verification_token'),
                Toggle::make('is_verified')
                    ->label(__('keys.verified')),
                DateTimePicker::make('verified_at'),
            ]);
    }
}
