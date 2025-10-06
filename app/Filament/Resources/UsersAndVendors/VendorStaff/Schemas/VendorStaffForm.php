<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Schemas;

use App\Models\User;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorStaff;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class VendorStaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Header Information Section
                Section::make(__('keys.assignment_details'))
                    ->description(__('keys.assignment_details_description'))
                    ->icon('heroicon-o-link')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('keys.user_account'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->options(function () {
                                        return User::regularUsers()
                                            ->whereDoesntHave('vendor')
                                            ->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $context) {
                                        // Only run in update context, not create
                                        if ($context === 'edit') {
                                            echo "User changed to: " . $state . "\n";
                                            // Clear vendor selection if user changes
                                            if ($state) {
                                                $set('vendor_id', null);
                                            }
                                        }
                                    })
                                    ->helperText(__('keys.user_account_helper'))
                                    ->placeholder(__('keys.user_account_placeholder')),

                                Select::make('vendor_id')
                                    ->label(__('keys.vendor'))
                                    ->relationship('vendor', 'business_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->options(function () {
                                        return Vendor::pluck('business_name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $context) {
                                        // Only run in update context, not create
                                        if ($context === 'edit') {
                                            echo "Vendor changed to: " . $state . "\n";
                                            // Clear user selection if vendor changes
                                            if ($state) {
                                                $set('user_id', null);
                                            }
                                        }
                                    })
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                $userId = request()->input('user_id');
                                                if ($userId && VendorStaff::where('vendor_id', $value)->where('user_id', $userId)->exists()) {
                                                    $fail(__('keys.user_already_staff'));
                                                }
                                            };
                                        },
                                    ])
                                    ->helperText(__('keys.vendor_helper'))
                                    ->placeholder(__('keys.vendor_placeholder')),
                            ]),
                    ]),

                                // Employment Details Section
                Section::make(__('keys.employment_details'))
                    ->description(__('keys.employment_details_description'))
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('employment_type')
                                    ->label(__('keys.employment_type'))
                                    ->options([
                                        'full_time' => __('keys.full_time'),
                                        'part_time' => __('keys.part_time'),
                                        'contract' => __('keys.contract'),
                                        'temporary' => __('keys.temporary'),
                                    ])
                                    ->required()
                                    ->placeholder(__('keys.employment_type_placeholder'))
                                    ->helperText(__('keys.employment_type_helper')),

                                TextInput::make('department')
                                    ->label(__('keys.department'))
                                    ->maxLength(255)
                                    ->placeholder(__('keys.department_placeholder'))
                                    ->helperText(__('keys.department_helper')),
                            ]),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('hire_date')
                                    ->label(__('keys.hire_date'))
                                    ->required()
                                    ->maxDate(now())
                                    ->placeholder(__('keys.hire_date_placeholder'))
                                    ->helperText(__('keys.hire_date_helper')),

                                TextInput::make('salary')
                                    ->label(__('keys.monthly_salary'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(999999.99)
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->placeholder(__('keys.monthly_salary_placeholder'))
                                    ->helperText(__('keys.monthly_salary_helper')),

                                Select::make('work_schedule')
                                    ->label(__('keys.work_schedule'))
                                    ->options([
                                        'morning' => __('keys.morning_shift'),
                                        'afternoon' => __('keys.afternoon_shift'),
                                        'night' => __('keys.night_shift'),
                                        'flexible' => __('keys.flexible_schedule'),
                                    ])
                                    ->placeholder(__('keys.work_schedule_placeholder'))
                                    ->helperText(__('keys.work_schedule_helper')),
                            ]),

                        Textarea::make('job_description')
                            ->label(__('keys.job_description'))
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder(__('keys.job_description_placeholder'))
                            ->helperText(__('keys.job_description_helper')),
                    ]),

                // Contact Information Section
                Section::make(__('keys.contact_information'))
                    ->description(__('keys.contact_information_description'))
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label(__('keys.work_email'))
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(table: 'vendor_staff', column: 'email', ignoreRecord: true)
                                    ->placeholder(__('keys.work_email_placeholder'))
                                    ->helperText(__('keys.work_email_helper')),

                                TextInput::make('phone')
                                    ->label(__('keys.work_phone'))
                                    ->tel()
                                    ->maxLength(20)
                                    ->regex('/^\+?[\d\s\-\(\)]+$/')
                                    ->placeholder(__('keys.work_phone_placeholder'))
                                    ->helperText(__('keys.work_phone_helper')),
                            ]),

                        Textarea::make('address')
                            ->label(__('keys.work_address'))
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder(__('keys.work_address_placeholder'))
                            ->helperText(__('keys.work_address_helper')),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('emergency_contact_name')
                                    ->label(__('keys.emergency_contact_name'))
                                    ->maxLength(255)
                                    ->placeholder(__('keys.emergency_contact_name_placeholder'))
                                    ->helperText(__('keys.emergency_contact_name_helper')),

                                TextInput::make('emergency_contact_phone')
                                    ->label(__('keys.emergency_contact_phone'))
                                    ->tel()
                                    ->maxLength(20)
                                    ->regex('/^\+?[\d\s\-\(\)]+$/')
                                    ->placeholder(__('keys.emergency_contact_phone_placeholder'))
                                    ->helperText(__('keys.emergency_contact_phone_helper')),
                            ]),
                    ]),

                // Compensation Section
                Section::make(__('keys.compensation_benefits'))
                    ->description(__('keys.compensation_benefits_description'))
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('salary')
                                    ->label(__('keys.monthly_salary'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->step(0.01)
                                    ->placeholder(__('keys.monthly_salary_placeholder'))
                                    ->helperText(__('keys.monthly_salary_helper')),

                                TextInput::make('commission_rate')
                                    ->label(__('keys.commission_rate'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(999.99)
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->placeholder(__('keys.commission_rate_placeholder'))
                                    ->helperText(__('keys.commission_rate_helper')),
                            ]),

                        Placeholder::make('compensation_summary')
                            ->label('')
                            ->content(function ($get) {
                                $salary = $get('salary') ?? 0;
                                $commission = $get('commission_rate') ?? 0;
                                return __('keys.compensation_summary', [
                                    'salary' => number_format($salary, 2),
                                    'commission' => $commission
                                ]);
                            })
                            ->visible(function ($get) {
                                return $get('salary') || $get('commission_rate');
                            }),
                    ]),

                                // Permissions and Access Section
                Section::make(__('keys.permissions_and_access'))
                    ->description(__('keys.permissions_and_access_description'))
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('role')
                                    ->label(__('keys.staff_role'))
                                    ->options([
                                        'admin' => __('keys.admin'),
                                        'manager' => __('keys.manager'),
                                        'sales' => __('keys.sales'),
                                        'support' => __('keys.support'),
                                        'warehouse' => __('keys.warehouse'),
                                        'driver' => __('keys.driver'),
                                    ])
                                    ->required()
                                    ->placeholder(__('keys.staff_role_placeholder'))
                                    ->helperText(__('keys.staff_role_helper')),

                                Toggle::make('can_manage_inventory')
                                    ->label(__('keys.can_manage_inventory'))
                                    ->helperText(__('keys.can_manage_inventory_helper'))
                                    ->default(false),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('can_handle_sales')
                                    ->label(__('keys.can_handle_sales'))
                                    ->helperText(__('keys.can_handle_sales_helper'))
                                    ->default(false),

                                Toggle::make('can_process_orders')
                                    ->label(__('keys.can_process_orders'))
                                    ->helperText(__('keys.can_process_orders_helper'))
                                    ->default(false),

                                Toggle::make('can_access_reports')
                                    ->label(__('keys.can_access_reports'))
                                    ->helperText(__('keys.can_access_reports_helper'))
                                    ->default(false),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('can_manage_staff')
                                    ->label(__('keys.can_manage_staff'))
                                    ->helperText(__('keys.can_manage_staff_helper'))
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label(__('keys.account_status'))
                                    ->helperText(__('keys.account_status_helper'))
                                    ->default(true),
                            ]),
                    ]),

                // Additional Information Section
                Section::make(__('keys.additional_information'))
                    ->description(__('keys.additional_information_description'))
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('keys.internal_notes'))
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder(__('keys.internal_notes_placeholder'))
                            ->helperText(__('keys.internal_notes_helper')),

                        FileUpload::make('documents')
                            ->label(__('keys.supporting_documents'))
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120) // 5MB
                            ->directory('vendor-staff-documents')
                            ->visibility('private')
                            ->helperText(__('keys.supporting_documents_helper')),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('contract_end_date')
                                    ->label(__('keys.contract_end_date'))
                                    ->minDate(now())
                                    ->placeholder(__('keys.contract_end_date_placeholder'))
                                    ->helperText(__('keys.contract_end_date_helper')),

                                TextInput::make('probation_period_months')
                                    ->label(__('keys.probation_period_months'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(24)
                                    ->placeholder(__('keys.probation_period_months_placeholder'))
                                    ->helperText(__('keys.probation_period_months_helper')),
                            ]),
                    ]),
            ]);
    }
}
