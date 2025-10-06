<?php

namespace App\Filament\Resources\Locations\Countries\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CountriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Country Information')
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('keys.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('keys.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('keys.country_code'))
                            ->required()
                            ->maxLength(2)
                            ->rules(['regex:/^[A-Z]{2}$/'])
                            ->placeholder('e.g., SA, AE, EG')
                            ->helperText('2-letter ISO country code')
                            ->uppercase(),

                        TextInput::make('phone_code')
                            ->label(__('keys.phone_code'))
                            ->maxLength(5)
                            ->placeholder('e.g., +966, +971')
                            ->helperText('Country calling code'),

                        TextInput::make('currency_code')
                            ->label(__('keys.currency_code'))
                            ->maxLength(3)
                            ->rules(['regex:/^[A-Z]{3}$/'])
                            ->placeholder('e.g., SAR, AED, EGP')
                            ->helperText('3-letter ISO currency code')
                            ->uppercase(),

                        TextInput::make('flag_url')
                            ->label(__('keys.flag_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://example.com/flags/sa.png'),

                        Toggle::make('is_active')
                            ->label(__('keys.active'))
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
