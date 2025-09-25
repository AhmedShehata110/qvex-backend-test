<?php

namespace App\Filament\Resources\Locations\Countries\Schemas;

use Filament\Forms\Components\Section;
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
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Country Code (ISO)')
                            ->required()
                            ->maxLength(2)
                            ->rules(['regex:/^[A-Z]{2}$/'])
                            ->placeholder('e.g., SA, AE, EG')
                            ->helperText('2-letter ISO country code')
                            ->uppercase(),

                        TextInput::make('phone_code')
                            ->label('Phone Code')
                            ->maxLength(5)
                            ->placeholder('e.g., +966, +971')
                            ->helperText('Country calling code'),

                        TextInput::make('currency_code')
                            ->label('Currency Code')
                            ->maxLength(3)
                            ->rules(['regex:/^[A-Z]{3}$/'])
                            ->placeholder('e.g., SAR, AED, EGP')
                            ->helperText('3-letter ISO currency code')
                            ->uppercase(),

                        TextInput::make('flag_url')
                            ->label('Flag URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://example.com/flags/sa.png'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
