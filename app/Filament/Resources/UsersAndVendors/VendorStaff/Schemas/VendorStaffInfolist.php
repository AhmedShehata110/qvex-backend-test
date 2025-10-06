<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class VendorStaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Staff Information')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('keys.staff_name'))
                                    ->size(TextSize::Large)
                                    ->weight('bold'),

                                TextEntry::make('employee_id')
                                    ->label(__('keys.id'))
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('vendor.business_name')
                                    ->label(__('keys.vendor'))
                                    ->icon('heroicon-m-building-office')
                                    ->placeholder(__('keys.no_vendor_assigned')),

                                TextEntry::make('position')
                                    ->label(__('keys.position'))
                                    ->placeholder(__('keys.not_specified')),

                                TextEntry::make('role')
                                    ->label(__('keys.position_title'))
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('department')
                                    ->label(__('keys.department'))
                                    ->placeholder(__('keys.not_specified')),

                                TextEntry::make('employment_status')
                                    ->label(__('keys.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'full_time' => 'success',
                                        'part_time' => 'warning',
                                        'contract' => 'info',
                                        'intern' => 'gray',
                                        'terminated' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('hire_date')
                                    ->label(__('keys.hire_date'))
                                    ->date()
                                    ->placeholder(__('keys.not_specified')),
                            ]),

                        Section::make('Contact Information')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label(__('keys.email'))
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->placeholder(__('keys.not_provided')),

                                TextEntry::make('phone')
                                    ->label(__('keys.work_phone'))
                                    ->icon('heroicon-m-phone')
                                    ->copyable()
                                    ->placeholder(__('keys.not_provided')),

                                TextEntry::make('address')
                                    ->label(__('keys.address'))
                                    ->placeholder(__('keys.not_provided'))
                                    ->columnSpanFull(),

                                TextEntry::make('emergency_contact_name')
                                    ->label(__('keys.emergency_contact'))
                                    ->icon('heroicon-m-user-group')
                                    ->placeholder(__('keys.not_provided')),

                                TextEntry::make('emergency_contact_phone')
                                    ->label(__('keys.emergency_phone'))
                                    ->icon('heroicon-m-phone')
                                    ->copyable()
                                    ->placeholder(__('keys.not_provided')),
                            ]),

                        Section::make('Compensation & Permissions')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('salary')
                                    ->label(__('keys.salary'))
                                    ->money('USD')
                                    ->placeholder(__('keys.not_specified')),

                                TextEntry::make('commission_rate')
                                    ->label(__('keys.commission'))
                                    ->suffix('%')
                                    ->placeholder(__('keys.not_specified')),

                                TextEntry::make('permissions')
                                    ->label(__('keys.permissions'))
                                    ->listWithLineBreaks()
                                    ->placeholder(__('keys.no_permissions_assigned'))
                                    ->columnSpanFull(),

                                TextEntry::make('notes')
                                    ->label(__('keys.notes'))
                                    ->placeholder(__('keys.no_notes'))
                                    ->columnSpanFull(),

                                IconEntry::make('is_active')
                                    ->label(__('keys.status'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('last_active_at')
                                    ->label(__('keys.active'))
                                    ->dateTime()
                                    ->placeholder(__('keys.never')),
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label(__('keys.created'))
                                            ->dateTime(),

                                        TextEntry::make('updated_at')
                                            ->label(__('keys.updated'))
                                            ->dateTime(),

                                        TextEntry::make('deleted_at')
                                            ->label(__('keys.deleted_at'))
                                            ->dateTime()
                                            ->placeholder(__('keys.not_deleted')),
                                    ]),
                            ])
                            ->collapsible(),
                    ])->columnSpanFull(),

            ]);
    }
}
