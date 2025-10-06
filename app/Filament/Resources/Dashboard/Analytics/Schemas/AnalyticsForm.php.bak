<?php

namespace App\Filament\Resources\Dashboard\Analytics\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnalyticsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Analytics Information')
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'page_view' => 'Page View',
                                'user_action' => 'User Action',
                                'conversion' => 'Conversion',
                                'error' => 'Error',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('entity_type')
                            ->label('Entity Type')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('entity_id')
                            ->label('Entity ID')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('metric')
                            ->label('Metric')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('value')
                            ->label('Value')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01),

                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->default(now()),

                        KeyValue::make('metadata')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addable()
                            ->deletable()
                            ->editableKeys()
                            ->editableValues(),
                    ])
                    ->columns(2),
            ]);
    }
}
