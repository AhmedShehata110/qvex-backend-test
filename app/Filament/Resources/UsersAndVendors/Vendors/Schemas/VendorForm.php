<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vendor Information')
                    ->description('Basic vendor details and contact information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Vendor Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('The official name of the vendor/dealership'),

                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash'])
                                    ->helperText('Used in URLs - auto-generated from name'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Primary contact email'),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Primary contact phone number'),
                            ]),

                        TextInput::make('website')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Vendor\'s official website (optional)'),

                        Textarea::make('address')
                            ->label('Business Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Full business address including city and postal code'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Brief description of the vendor and services offered'),
                    ]),

                Section::make('Vendor Settings')
                    ->description('Vendor configuration and ownership')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('owner_id')
                                    ->label('Vendor Owner')
                                    ->relationship('owner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User who owns/manages this vendor'),

                                Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->default(true)
                                    ->helperText('Whether this vendor is currently active'),
                            ]),
                    ]),
            ]);
    }
}
