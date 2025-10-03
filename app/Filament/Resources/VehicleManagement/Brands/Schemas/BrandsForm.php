<?php

namespace App\Filament\Resources\VehicleManagement\Brands\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use App\Models\Vehicle\VehicleMake;

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
                                TextInput::make('name.en')
                                    ->label('Brand Name (English)*')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('name.ar')
                                    ->label('Arabic Name')
                                    ->maxLength(255)
                                    ->helperText('Brand name in Arabic'),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(VehicleMake::class, 'slug', ignoreRecord: true)
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
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Brand Logo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageResizeTargetWidth(50)
                            ->imageResizeTargetHeight(50)
                            ->collection('logos')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->helperText('Upload brand logo (max 2MB). SVG, PNG, JPG, or WebP format.'),
                    ]),
            ]);
    }
}
