<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorStaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff Information')
                    ->description('Basic staff member details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('User Account')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Select the user account for this staff member'),

                                Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'business_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Vendor this staff member works for'),
                            ]),

                        Grid::make(2)
                            ->schema([

                                TextInput::make('position')
                                    ->label('Position')
                                    ->maxLength(100)
                                    ->required()
                                    ->helperText('Job position/title'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('role')
                                    ->label('Role')
                                    ->options([
                                        'manager' => 'Manager',
                                        'sales' => 'Sales Representative',
                                        'finance' => 'Finance',
                                        'service' => 'Service Technician',
                                        'support' => 'Customer Support',
                                        'admin' => 'Administrator',
                                    ])
                                    ->default('sales')
                                    ->required()
                                    ->helperText('Primary role/responsibility'),

                                TextInput::make('department')
                                    ->label('Department')
                                    ->maxLength(100)
                                    ->helperText('Department or division'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('employment_status')
                                    ->label('Employment Status')
                                    ->options([
                                        'full_time' => 'Full Time',
                                        'part_time' => 'Part Time',
                                        'contract' => 'Contract',
                                        'intern' => 'Intern',
                                    ])
                                    ->default('full_time')
                                    ->required()
                                    ->helperText('Employment type'),

                                DatePicker::make('hire_date')
                                    ->label('Hire Date')
                                    ->default(now())
                                    ->required()
                                    ->helperText('Date when staff member was hired'),
                            ]),
                    ]),

                Section::make('Contact & Emergency Information')
                    ->description('Contact details and emergency contacts')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Work Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->helperText('Work email address'),

                                TextInput::make('phone')
                                    ->label('Work Phone')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Work phone number'),
                            ]),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Staff member\'s address'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('emergency_contact_name')
                                    ->label('Emergency Contact Name')
                                    ->maxLength(255)
                                    ->helperText('Name of emergency contact person'),

                                TextInput::make('emergency_contact_phone')
                                    ->label('Emergency Contact Phone')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Emergency contact phone number'),
                            ]),
                    ]),

                Section::make('Compensation & Permissions')
                    ->description('Salary, commission, and access permissions')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('salary')
                                    ->label('Salary')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->helperText('Monthly salary amount'),

                                TextInput::make('commission_rate')
                                    ->label('Commission Rate (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->helperText('Commission percentage for sales'),
                            ]),

                        Select::make('permissions')
                            ->label('Permissions')
                            ->multiple()
                            ->options([
                                'manage_vehicles' => 'Manage Vehicles',
                                'manage_inquiries' => 'Manage Inquiries',
                                'manage_transactions' => 'Manage Transactions',
                                'manage_customers' => 'Manage Customers',
                                'manage_reports' => 'View Reports',
                                'manage_settings' => 'Manage Settings',
                                'manage_staff' => 'Manage Staff',
                                'manage_finances' => 'Manage Finances',
                            ])
                            ->helperText('Select permissions for this staff member'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Additional notes about the staff member'),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->default(true)
                                    ->helperText('Whether this staff member is currently active'),
                            ]),
                    ]),
            ]);
    }
}
