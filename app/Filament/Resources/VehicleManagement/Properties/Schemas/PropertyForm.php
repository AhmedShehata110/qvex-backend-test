<?php

namespace App\Filament\Resources\VehicleManagement\Properties\Schemas;

use App\Models\Vehicle\VehicleFeature;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Feature Information')
                    ->description('Basic information about the vehicle feature.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->label('Feature Name'),

                                TextInput::make('name_ar')
                                    ->maxLength(255)
                                    ->label('Arabic Name')
                                    ->helperText('Feature name in Arabic'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(VehicleFeature::class, 'slug', ignoreRecord: true)
                                    ->rules(['alpha_dash'])
                                    ->label('Slug')
                                    ->helperText('URL-friendly identifier'),

                                Select::make('category')
                                    ->options(VehicleFeature::getCategories())
                                    ->searchable()
                                    ->label('Category')
                                    ->required(),
                            ]),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(9999)
                            ->label('Sort Order')
                            ->helperText('Lower numbers appear first'),

                        Toggle::make('is_premium')
                            ->label('Premium Feature')
                            ->default(false)
                            ->helperText('Mark as premium feature'),
                    ]),

                Section::make('Feature Media')
                    ->description('Upload feature icon and images.')
                    ->schema([
                        FileUpload::make('icon')
                            ->label('Feature Icon')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->imageResizeTargetWidth(64)
                            ->imageResizeTargetHeight(64)
                            ->directory('features/icons')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->maxSize(1024)
                            ->helperText('Upload feature icon (max 1MB). SVG, PNG, JPG, or WebP format.'),
                    ]),
            ]);
    }
}
