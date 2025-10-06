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
                                    ->label('Staff Name')
                                    ->size(TextSize::Large)
                                    ->weight('bold'),

                                TextEntry::make('employee_id')
                                    ->label('Employee ID')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('vendor.business_name')
                                    ->label('Vendor')
                                    ->icon('heroicon-m-building-office')
                                    ->placeholder('No vendor assigned'),

                                TextEntry::make('position')
                                    ->label('Position')
                                    ->placeholder('Not specified'),

                                TextEntry::make('role')
                                    ->label('Role')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('department')
                                    ->label('Department')
                                    ->placeholder('Not specified'),

                                TextEntry::make('employment_status')
                                    ->label('Employment Status')
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
                                    ->label('Hire Date')
                                    ->date()
                                    ->placeholder('Not specified'),
                            ]),

                        Section::make('Contact Information')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label('Work Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                TextEntry::make('phone')
                                    ->label('Work Phone')
                                    ->icon('heroicon-m-phone')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                TextEntry::make('address')
                                    ->label('Address')
                                    ->placeholder('Not provided')
                                    ->columnSpanFull(),

                                TextEntry::make('emergency_contact_name')
                                    ->label('Emergency Contact')
                                    ->icon('heroicon-m-user-group')
                                    ->placeholder('Not provided'),

                                TextEntry::make('emergency_contact_phone')
                                    ->label('Emergency Phone')
                                    ->icon('heroicon-m-phone')
                                    ->copyable()
                                    ->placeholder('Not provided'),
                            ]),

                        Section::make('Compensation & Permissions')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('salary')
                                    ->label('Salary')
                                    ->money('USD')
                                    ->placeholder('Not specified'),

                                TextEntry::make('commission_rate')
                                    ->label('Commission Rate')
                                    ->suffix('%')
                                    ->placeholder('Not specified'),

                                TextEntry::make('permissions')
                                    ->label('Permissions')
                                    ->listWithLineBreaks()
                                    ->placeholder('No permissions assigned')
                                    ->columnSpanFull(),

                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->placeholder('No notes')
                                    ->columnSpanFull(),

                                IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('last_active_at')
                                    ->label('Last Active')
                                    ->dateTime()
                                    ->placeholder('Never'),
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Created')
                                            ->dateTime(),

                                        TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->dateTime(),

                                        TextEntry::make('deleted_at')
                                            ->label('Deleted')
                                            ->dateTime()
                                            ->placeholder('Not deleted'),
                                    ]),
                            ])
                            ->collapsible(),
                    ])->columnSpanFull(),

            ]);
    }
}
