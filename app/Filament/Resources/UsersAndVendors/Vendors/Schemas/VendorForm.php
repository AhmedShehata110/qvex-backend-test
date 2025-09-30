<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
                                TextInput::make('business_name')
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

                        Grid::make(3)
                            ->schema([
                                TextInput::make('registration_number')
                                    ->label('Registration Number')
                                    ->maxLength(100)
                                    ->helperText('Business registration number'),

                                TextInput::make('tax_id')
                                    ->label('Tax ID')
                                    ->maxLength(100)
                                    ->helperText('Tax identification number'),

                                TextInput::make('trade_license')
                                    ->label('Trade License')
                                    ->maxLength(100)
                                    ->helperText('Trade license number'),
                            ]),

                        TextInput::make('website')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Vendor\'s official website (optional)'),

                        TextInput::make('business_name_ar')
                            ->label('Business Name (Arabic)')
                            ->maxLength(255)
                            ->helperText('Arabic version of the business name'),

                        Select::make('vendor_type')
                            ->label('Vendor Type')
                            ->options([
                                'dealership' => 'Car Dealership',
                                'rental_company' => 'Rental Company',
                                'individual' => 'Individual Seller',
                                'service_center' => 'Service Center',
                            ])
                            ->default('dealership')
                            ->required()
                            ->helperText('Type of vendor business'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Brief description of the vendor and services offered'),

                        Textarea::make('description_ar')
                            ->label('Description (Arabic)')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Arabic description of the vendor and services offered'),
                    ]),

                Section::make('Vendor Settings')
                    ->description('Vendor configuration and ownership')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Vendor Owner')
                                    ->relationship('owner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User who owns/manages this vendor'),

                                TextInput::make('commission_rate')
                                    ->label('Commission Rate (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(5.00)
                                    ->suffix('%')
                                    ->helperText('Commission percentage for this vendor'),

                                Toggle::make('is_featured')
                                    ->label('Featured Vendor')
                                    ->default(false)
                                    ->helperText('Display this vendor prominently'),

                                Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->default(true)
                                    ->helperText('Whether this vendor is currently active'),
                            ]),
                    ]),
            ]);
    }
}
