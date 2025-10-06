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
                            ->label(__('keys.title'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label(__('keys.description'))
                            ->maxLength(1000)
                            ->rows(3),

                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label(__('keys.type'))
                                    ->options([
                                        'banner' => 'Banner',
                                        'popup' => 'Popup',
                                        'sidebar' => 'Sidebar',
                                        'email' => 'Email',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('position')
                                    ->label(__('keys.position'))
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
                            ->label(__('keys.target_url'))
                            ->url()
                            ->maxLength(500),

                        TextInput::make('image_url')
                            ->label(__('keys.image_url'))
                            ->url()
                            ->maxLength(500),
                    ]),

                Section::make('Scheduling')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label(__('keys.start_date'))
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('end_date')
                                    ->label(__('keys.end_date'))
                                    ->placeholder('No end date'),
                            ]),

                        Toggle::make('is_active')
                            ->label(__('keys.active'))
                            ->default(true),
                    ]),

                Section::make('Performance & Budget')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('click_count')
                                    ->label(__('keys.click_count'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('view_count')
                                    ->label(__('keys.view_count'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('budget')
                                    ->label(__('keys.budget'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('spent')
                                    ->label(__('keys.spent'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('priority')
                                    ->label(__('keys.priority'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(10),

                                KeyValue::make('target_audience')
                                    ->label(__('keys.target_audience'))
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
