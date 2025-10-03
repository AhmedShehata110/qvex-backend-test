<?php

namespace App\Filament\Resources\VehicleManagement\Colors\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ColorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Color Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Color Name (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('name.ar')
                                    ->label('Color Name (Arabic)')
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                ColorPicker::make('hex_code')
                                    ->label('Color')
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        TextInput::make('rgb_value')
                            ->label('RGB Value')
                            ->placeholder('e.g., 255,255,255')
                            ->helperText('Enter RGB values separated by commas')
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'exterior' => 'Exterior',
                                'interior' => 'Interior',
                            ])
                            ->default('exterior')
                            ->required()
                            ->native(false),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_metallic')
                                    ->label('Metallic')
                                    ->default(false),

                                Toggle::make('is_popular')
                                    ->label('Popular')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
