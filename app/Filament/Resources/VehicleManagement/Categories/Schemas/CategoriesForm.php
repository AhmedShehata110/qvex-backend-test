<?php

namespace App\Filament\Resources\VehicleManagement\Categories\Schemas;

use App\Models\Vehicle\VehicleModel;
use Filament\Forms\Components\Card;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('Basic Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('make_id')
                                            ->label('Vehicle Make')
                                            ->relationship('make', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Make Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('name_ar')
                                                    ->label('Arabic Name')
                                                    ->maxLength(255),
                                                TextInput::make('country')
                                                    ->label('Country of Origin')
                                                    ->maxLength(100),
                                                Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true),
                                            ])
                                            ->columnSpanFull(),

                                        TextInput::make('name')
                                            ->label('Model Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('slug', \Illuminate\Support\Str::slug($state));
                                            }),

                                        TextInput::make('name_ar')
                                            ->label('Arabic Name')
                                            ->maxLength(255),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(VehicleModel::class, 'slug', ignoreRecord: true)
                                            ->rules(['alpha_dash'])
                                            ->helperText('Auto-generated from name. Use lowercase letters, numbers, and dashes only.'),

                                        Select::make('body_type')
                                            ->label('Body Type')
                                            ->options(VehicleModel::getBodyTypes())
                                            ->searchable()
                                            ->required(),
                                    ]),
                            ]),

                        Section::make('Production Years')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('year_start')
                                            ->label('Start Year')
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue((int) date('Y') + 5)
                                            ->required()
                                            ->placeholder('e.g. 2020'),

                                        TextInput::make('year_end')
                                            ->label('End Year')
                                            ->numeric()
                                            ->minValue(function ($get) {
                                                return $get('year_start') ?: 1900;
                                            })
                                            ->maxValue((int) date('Y') + 5)
                                            ->nullable()
                                            ->placeholder('Leave empty if still in production')
                                            ->helperText('Leave blank if model is still in production'),
                                    ]),
                            ]),

                        Section::make('Settings')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->helperText('Lower numbers appear first'),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->helperText('Only active models are shown to users'),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->schema([
                        Section::make('Additional Information')
                            ->schema([
                                Select::make('added_by_id')
                                    ->label('Added By')
                                    ->relationship('addedBy', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id())
                                    ->required()
                                    ->disabled(fn ($context) => $context === 'edit')
                                    ->dehydrated(fn ($context) => $context === 'create'),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
