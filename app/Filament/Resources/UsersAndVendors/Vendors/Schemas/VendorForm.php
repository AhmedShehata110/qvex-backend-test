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
                    ->description(__('keys.basic_vendor_details_and_contact_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('business_name')
                                    ->label(__('keys.vendor_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('The official name of the vendor/dealership'),

                                TextInput::make('slug')
                                    ->label(__('keys.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash'])
                                    ->helperText('Used in URLs - auto-generated from name'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('registration_number')
                                    ->label(__('keys.registration_number'))
                                    ->maxLength(100)
                                    ->helperText('Business registration number'),

                                TextInput::make('tax_id')
                                    ->label(__('keys.id'))
                                    ->maxLength(100)
                                    ->helperText('Tax identification number'),

                                TextInput::make('trade_license')
                                    ->label(__('keys.trade_license'))
                                    ->maxLength(100)
                                    ->helperText('Trade license number'),
                            ]),

                        TextInput::make('website')
                            ->label(__('keys.website_url'))
                            ->url()
                            ->maxLength(255)
                            ->helperText('Vendor\'s official website (optional)'),

                        TextInput::make('business_name_ar')
                            ->label(__('keys.business_name'))
                            ->maxLength(255)
                            ->helperText('Arabic version of the business name'),

                        Select::make('vendor_type')
                            ->label(__('keys.type'))
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
                            ->label(__('keys.description'))
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Brief description of the vendor and services offered'),

                        Textarea::make('description_ar')
                            ->label(__('keys.description'))
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Arabic description of the vendor and services offered'),
                    ]),

                Section::make('Vendor Settings')
                    ->description(__('keys.vendor_configuration_and_ownership'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('keys.vendor_owner'))
                                    ->relationship('owner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User who owns/manages this vendor'),

                                TextInput::make('commission_rate')
                                    ->label(__('keys.commission'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(5.00)
                                    ->suffix('%')
                                    ->helperText('Commission percentage for this vendor'),

                                Toggle::make('is_featured')
                                    ->label(__('keys.vendor'))
                                    ->default(false)
                                    ->helperText('Display this vendor prominently'),

                                Toggle::make('is_active')
                                    ->label(__('keys.status'))
                                    ->default(true)
                                    ->helperText('Whether this vendor is currently active'),
                            ]),
                    ]),
            ]);
    }
}
