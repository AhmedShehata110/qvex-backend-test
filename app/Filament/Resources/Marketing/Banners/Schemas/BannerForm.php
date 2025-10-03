<?php

namespace App\Filament\Resources\Marketing\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('title_ar'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->image()
                    ->required()
                    ->collection('banners'),
                SpatieMediaLibraryFileUpload::make('image_mobile')
                    ->image()
                    ->collection('banners-mobile'),
                TextInput::make('link_url'),
                TextInput::make('link_text'),
                TextInput::make('link_text_ar'),
                Select::make('position')
                    ->options([
                        'hero' => 'Hero',
                        'sidebar' => 'Sidebar',
                        'footer' => 'Footer',
                        'popup' => 'Popup',
                        'in_content' => 'In content',
                    ])
                    ->required(),
                Select::make('type')
                    ->options(['promotional' => 'Promotional', 'informational' => 'Informational', 'vendor_ad' => 'Vendor ad'])
                    ->required(),
                TextInput::make('targeting'),
                Toggle::make('is_active')
                    ->required(),
                Select::make('added_by_id')
                    ->relationship('addedBy', 'name'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('click_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('expires_at'),
            ]);
    }
}
