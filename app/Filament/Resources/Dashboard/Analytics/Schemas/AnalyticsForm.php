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
                            ->label(__('keys.type'))
                            ->options([
                                'page_view' => 'Page View',
                                'user_action' => 'User Action',
                                'conversion' => 'Conversion',
                                'error' => 'Error',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('entity_type')
                            ->label(__('keys.type'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('entity_id')
                            ->label(__('keys.id'))
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('metric')
                            ->label(__('keys.dashboard_analytics_metric'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('value')
                            ->label(__('keys.dashboard_analytics_value'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01),

                        DatePicker::make('date')
                            ->label(__('keys.dashboard_analytics_date'))
                            ->required()
                            ->default(now()),

                        KeyValue::make('metadata')
                            ->label(__('keys.metadata'))
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
