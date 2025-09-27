<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BrandsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Information')
                    ->description('Basic information about the vehicle brand.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->label('Brand Name'),

                                TextInput::make('name_ar')
                                    ->maxLength(255)
                                    ->label('Arabic Name')
                                    ->helperText('Brand name in Arabic'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreDuringUpdate: true)
                                    ->rules(['alpha_dash'])
                                    ->label('Slug')
                                    ->helperText('URL-friendly identifier'),

                                Select::make('country_origin')
                                    ->options([
                                        'germany' => '🇩🇪 Germany',
                                        'japan' => '🇯🇵 Japan',
                                        'usa' => '🇺🇸 United States',
                                        'uk' => '🇬🇧 United Kingdom',
                                        'france' => '🇫🇷 France',
                                        'italy' => '🇮🇹 Italy',
                                        'south_korea' => '🇰🇷 South Korea',
                                        'sweden' => '🇸🇪 Sweden',
                                        'spain' => '🇪🇸 Spain',
                                        'czech_republic' => '🇨🇿 Czech Republic',
                                        'china' => '🇨🇳 China',
                                        'india' => '🇮🇳 India',
                                        'malaysia' => '🇲🇾 Malaysia',
                                        'other' => '🌍 Other',
                                    ])
                                    ->searchable()
                                    ->label('Country of Origin'),
                            ]),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(9999)
                            ->label('Sort Order')
                            ->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Brand Media')
                    ->description('Upload brand logo and images.')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Brand Logo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->resize(50)
                            ->optimize('webp')
                            ->directory('brands/logos')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->helperText('Upload brand logo (max 2MB). SVG, PNG, JPG, or WebP format.'),
                    ]),
            ]);
    }
}
