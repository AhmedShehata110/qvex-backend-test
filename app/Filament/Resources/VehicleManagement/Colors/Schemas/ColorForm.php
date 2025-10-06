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
                                    ->label(__('keys.color_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('name.ar')
                                    ->label(__('keys.color_name'))
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                ColorPicker::make('hex_code')
                                    ->label(__('keys.color'))
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        TextInput::make('rgb_value')
                            ->label(__('keys.rgb_value'))
                            ->placeholder(__('keys.eg_rgb'))
                            ->helperText('Enter RGB values separated by commas')
                            ->maxLength(255),

                        Select::make('type')
                            ->label(__('keys.type'))
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
                                    ->label(__('keys.metallic'))
                                    ->default(false),

                                Toggle::make('is_popular')
                                    ->label(__('keys.popular'))
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label(__('keys.sort_order'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
