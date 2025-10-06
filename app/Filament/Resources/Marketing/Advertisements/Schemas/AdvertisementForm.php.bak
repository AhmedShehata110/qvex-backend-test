<?php

namespace App\Filament\Resources\Marketing\Advertisements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->rows(3),

                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'banner' => 'Banner',
                                        'popup' => 'Popup',
                                        'sidebar' => 'Sidebar',
                                        'email' => 'Email',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('position')
                                    ->label('Position')
                                    ->options([
                                        'header' => 'Header',
                                        'footer' => 'Footer',
                                        'sidebar' => 'Sidebar',
                                        'content' => 'Content',
                                        'popup' => 'Popup',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Content & Links')
                    ->schema([
                        TextInput::make('target_url')
                            ->label('Target URL')
                            ->url()
                            ->maxLength(500),

                        TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->maxLength(500),
                    ]),

                Section::make('Scheduling')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->placeholder('No end date'),
                            ]),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Section::make('Performance & Budget')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('click_count')
                                    ->label('Click Count')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('view_count')
                                    ->label('View Count')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('budget')
                                    ->label('Budget')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('spent')
                                    ->label('Spent')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('priority')
                                    ->label('Priority')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(10),

                                KeyValue::make('target_audience')
                                    ->label('Target Audience')
                                    ->keyLabel('Criteria')
                                    ->valueLabel('Value')
                                    ->addable()
                                    ->deletable()
                                    ->editableKeys()
                                    ->editableValues(),
                            ]),
                    ]),
            ]);
    }
}
