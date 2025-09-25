<?php

namespace App\Filament\Resources\VehicleManagement\Vehicles\Schemas;

use App\Models\Vehicle\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Vehicle Information')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Section::make('Vehicle Details')
                                    ->description('Core vehicle information and specifications')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('vendor_id')
                                                    ->label('Vendor')
                                                    ->relationship('vendor', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),

                                                Select::make('make_id')
                                                    ->label('Make')
                                                    ->relationship('make', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live(),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                Select::make('model_id')
                                                    ->label('Model')
                                                    ->relationship('model', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live(),

                                                Select::make('trim_id')
                                                    ->label('Trim')
                                                    ->relationship('trim', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('year')
                                                    ->label('Year')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(1900)
                                                    ->maxValue(date('Y') + 1),

                                                Select::make('condition')
                                                    ->label('Condition')
                                                    ->options([
                                                        Vehicle::CONDITION_NEW => 'New',
                                                        Vehicle::CONDITION_USED => 'Used',
                                                        Vehicle::CONDITION_CERTIFIED_PREOWNED => 'Certified Pre-owned',
                                                        Vehicle::CONDITION_SALVAGE => 'Salvage',
                                                    ])
                                                    ->required(),

                                                Select::make('availability_type')
                                                    ->label('Availability')
                                                    ->options([
                                                        Vehicle::AVAILABILITY_SALE => 'For Sale',
                                                        Vehicle::AVAILABILITY_RENT => 'For Rent',
                                                        Vehicle::AVAILABILITY_BOTH => 'Both',
                                                    ])
                                                    ->required()
                                                    ->live(),
                                            ]),

                                        TextInput::make('vin')
                                            ->label('VIN Number')
                                            ->maxLength(17)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('17-character Vehicle Identification Number'),
                                    ]),

                                Section::make('Listing Information')
                                    ->description('Title, description, and listing details')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Vehicle Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                                if ($operation === 'create') {
                                                    $set('slug', Str::slug($state));
                                                }
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->rules(['alpha_dash'])
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(4)
                                            ->maxLength(2000)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pricing & Rental')
                            ->schema([
                                Section::make('Sale Pricing')
                                    ->description('Pricing information for vehicle sales')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('price')
                                                    ->label('Current Price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required(),

                                                TextInput::make('original_price')
                                                    ->label('Original Price')
                                                    ->numeric()
                                                    ->prefix('$'),

                                                Toggle::make('is_negotiable')
                                                    ->label('Negotiable')
                                                    ->default(true),
                                            ]),
                                    ])
                                    ->visible(fn ($get) => in_array($get('availability_type'), [Vehicle::AVAILABILITY_SALE, Vehicle::AVAILABILITY_BOTH])),

                                Section::make('Rental Pricing')
                                    ->description('Rental rates and deposit information')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('rental_daily_rate')
                                                    ->label('Daily Rate')
                                                    ->numeric()
                                                    ->prefix('$'),

                                                TextInput::make('rental_weekly_rate')
                                                    ->label('Weekly Rate')
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('rental_monthly_rate')
                                                    ->label('Monthly Rate')
                                                    ->numeric()
                                                    ->prefix('$'),

                                                TextInput::make('security_deposit')
                                                    ->label('Security Deposit')
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ]),
                                    ])
                                    ->visible(fn ($get) => in_array($get('availability_type'), [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH])),
                            ]),

                        Tabs\Tab::make('Specifications')
                            ->schema([
                                Section::make('Technical Specifications')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('mileage')
                                                    ->label('Mileage')
                                                    ->numeric()
                                                    ->suffix('km'),

                                                TextInput::make('doors')
                                                    ->label('Number of Doors')
                                                    ->numeric()
                                                    ->minValue(2)
                                                    ->maxValue(8),

                                                TextInput::make('cylinders')
                                                    ->label('Cylinders')
                                                    ->numeric()
                                                    ->minValue(3)
                                                    ->maxValue(16),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('exterior_color')
                                                    ->label('Exterior Color')
                                                    ->maxLength(50),

                                                TextInput::make('interior_color')
                                                    ->label('Interior Color')
                                                    ->maxLength(50),
                                            ]),

                                        TextInput::make('license_plate')
                                            ->label('License Plate')
                                            ->maxLength(20),
                                    ]),
                            ]),

                        Tabs\Tab::make('Status & Settings')
                            ->schema([
                                Section::make('Listing Status')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('status')
                                                    ->label('Status')
                                                    ->options([
                                                        Vehicle::STATUS_DRAFT => 'Draft',
                                                        Vehicle::STATUS_ACTIVE => 'Active',
                                                        Vehicle::STATUS_SOLD => 'Sold',
                                                        Vehicle::STATUS_RENTED => 'Rented',
                                                        Vehicle::STATUS_INACTIVE => 'Inactive',
                                                        Vehicle::STATUS_PENDING_APPROVAL => 'Pending Approval',
                                                    ])
                                                    ->required()
                                                    ->default(Vehicle::STATUS_DRAFT),

                                                Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('is_featured')
                                                    ->label('Featured')
                                                    ->default(false),

                                                Toggle::make('is_urgent')
                                                    ->label('Urgent Sale')
                                                    ->default(false),

                                                DatePicker::make('featured_until')
                                                    ->label('Featured Until')
                                                    ->visible(fn ($get) => $get('is_featured')),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
