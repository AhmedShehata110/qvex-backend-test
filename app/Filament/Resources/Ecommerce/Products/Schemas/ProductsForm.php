<?php

namespace App\Filament\Resources\Ecommerce\Products\Schemas;

use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleMake;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleTrim;
use App\Models\Vendor\Vendor;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('title_ar')
                                    ->label('Title (Arabic)')
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label('Description (English)')
                                    ->rows(3)
                                    ->maxLength(1000),

                                Textarea::make('description_ar')
                                    ->label('Description (Arabic)')
                                    ->rows(3)
                                    ->maxLength(1000),

                                Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->options(Vendor::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('make_id')
                                    ->label('Make')
                                    ->options(VehicleMake::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('model_id', null);
                                        $set('trim_id', null);
                                    }),

                                Select::make('model_id')
                                    ->label('Model')
                                    ->options(function (callable $get) {
                                        $makeId = $get('make_id');
                                        if (!$makeId) return [];
                                        return VehicleModel::where('make_id', $makeId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('trim_id', null);
                                    }),

                                Select::make('trim_id')
                                    ->label('Trim')
                                    ->options(function (callable $get) {
                                        $modelId = $get('model_id');
                                        if (!$modelId) return [];
                                        return VehicleTrim::where('model_id', $modelId)->pluck('name', 'id');
                                    })
                                    ->searchable(),

                                TextInput::make('year')
                                    ->label('Year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1)
                                    ->required(),

                                TextInput::make('vin')
                                    ->label('VIN')
                                    ->maxLength(17)
                                    ->unique(ignoreRecord: true),
                            ]),
                    ]),

                Section::make('Pricing & Availability')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),

                                TextInput::make('original_price')
                                    ->label('Original Price')
                                    ->numeric()
                                    ->prefix('$'),

                                Toggle::make('is_negotiable')
                                    ->label('Negotiable'),
                            ]),

                        Select::make('availability_type')
                            ->label('Availability Type')
                            ->options(Vehicle::getAvailabilityTypes())
                            ->required()
                            ->live(),

                        Section::make('Rental Rates')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('rental_daily_rate')
                                            ->label('Daily Rate')
                                            ->numeric()
                                            ->prefix('$')
                                            ->visible(function (callable $get) {
                                                $type = $get('availability_type');
                                                return in_array($type, [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH]);
                                            }),

                                        TextInput::make('rental_weekly_rate')
                                            ->label('Weekly Rate')
                                            ->numeric()
                                            ->prefix('$')
                                            ->visible(function (callable $get) {
                                                $type = $get('availability_type');
                                                return in_array($type, [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH]);
                                            }),

                                        TextInput::make('rental_monthly_rate')
                                            ->label('Monthly Rate')
                                            ->numeric()
                                            ->prefix('$')
                                            ->visible(function (callable $get) {
                                                $type = $get('availability_type');
                                                return in_array($type, [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH]);
                                            }),
                                    ]),

                                TextInput::make('security_deposit')
                                    ->label('Security Deposit')
                                    ->numeric()
                                    ->prefix('$')
                                    ->visible(function (callable $get) {
                                        $type = $get('availability_type');
                                        return in_array($type, [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH]);
                                    }),
                            ])
                            ->visible(function (callable $get) {
                                $type = $get('availability_type');
                                return in_array($type, [Vehicle::AVAILABILITY_RENT, Vehicle::AVAILABILITY_BOTH]);
                            }),
                    ]),

                Section::make('Condition & Specifications')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('condition')
                                    ->label('Condition')
                                    ->options(Vehicle::getConditions())
                                    ->required(),

                                TextInput::make('mileage')
                                    ->label('Mileage')
                                    ->numeric(),

                                Select::make('mileage_unit')
                                    ->label('Mileage Unit')
                                    ->options([
                                        'km' => 'Kilometers',
                                        'mi' => 'Miles',
                                    ])
                                    ->default('km'),

                                TextInput::make('exterior_color')
                                    ->label('Exterior Color'),

                                TextInput::make('interior_color')
                                    ->label('Interior Color'),

                                TextInput::make('doors')
                                    ->label('Doors')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10),

                                TextInput::make('cylinders')
                                    ->label('Cylinders')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(16),

                                TextInput::make('license_plate')
                                    ->label('License Plate')
                                    ->maxLength(20),
                            ]),

                        KeyValue::make('additional_specs')
                            ->label('Additional Specifications')
                            ->keyLabel('Specification')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Specification'),
                    ]),

                Section::make('Location')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('city')
                                    ->label('City')
                                    ->required(),

                                TextInput::make('state')
                                    ->label('State/Province'),

                                TextInput::make('country')
                                    ->label('Country')
                                    ->default('Saudi Arabia'),

                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric(),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric(),
                            ]),
                    ]),

                Section::make('Warranty & Service')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('has_warranty')
                                    ->label('Has Warranty')
                                    ->live(),

                                Textarea::make('warranty_details')
                                    ->label('Warranty Details')
                                    ->rows(2)
                                    ->visible(function (callable $get) {
                                        return $get('has_warranty');
                                    }),

                                DatePicker::make('warranty_expires_at')
                                    ->label('Warranty Expires At')
                                    ->visible(function (callable $get) {
                                        return $get('has_warranty');
                                    }),

                                DatePicker::make('last_service_date')
                                    ->label('Last Service Date'),

                                TextInput::make('service_interval_km')
                                    ->label('Service Interval (KM)')
                                    ->numeric(),

                                Textarea::make('service_history')
                                    ->label('Service History')
                                    ->rows(3),
                            ]),
                    ]),

                Section::make('SEO & Marketing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->unique(ignoreRecord: true)
                                    ->rules(['regex:/^[a-z0-9-]+$/']),

                                TagsInput::make('seo_keywords')
                                    ->label('SEO Keywords')
                                    ->placeholder('Add keyword'),

                                Toggle::make('is_featured')
                                    ->label('Featured'),

                                Toggle::make('is_urgent')
                                    ->label('Urgent'),

                                DatePicker::make('featured_until')
                                    ->label('Featured Until')
                                    ->visible(function (callable $get) {
                                        return $get('is_featured');
                                    }),
                            ]),
                    ]),

                Section::make('Status & Approval')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Vehicle::getStatuses())
                                    ->required()
                                    ->default(Vehicle::STATUS_DRAFT),

                                DatePicker::make('approved_at')
                                    ->label('Approved At')
                                    ->readOnly(),

                                Select::make('approved_by')
                                    ->label('Approved By')
                                    ->options(\App\Models\User::query()->pluck('name', 'id'))
                                    ->readOnly(),

                                Textarea::make('rejection_reason')
                                    ->label('Rejection Reason')
                                    ->rows(2)
                                    ->readOnly(),
                            ]),
                    ]),
            ]);
    }
}
