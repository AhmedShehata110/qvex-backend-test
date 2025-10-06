<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VendorStaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('keys.staff_name'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('vendor.business_name')
                    ->label(__('keys.vendor'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('position')
                    ->label(__('keys.position'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->label(__('keys.position_title'))
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('department')
                    ->label(__('keys.department'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('employment_status')
                    ->label(__('keys.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'warning',
                        'contract' => 'info',
                        'intern' => 'gray',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('hire_date')
                    ->label(__('keys.hire_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('keys.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('keys.status'))
                    ->placeholder(__('keys.all_staff'))
                    ->trueLabel('Active staff')
                    ->falseLabel('Inactive staff'),

                SelectFilter::make('role')
                    ->label(__('keys.position_title'))
                    ->options([
                        'manager' => 'Manager',
                        'sales' => 'Sales',
                        'finance' => 'Finance',
                        'service' => 'Service',
                        'support' => 'Support',
                        'admin' => 'Admin',
                    ])
                    ->searchable(),

                SelectFilter::make('employment_status')
                    ->label(__('keys.status'))
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'intern' => 'Intern',
                        'terminated' => 'Terminated',
                    ])
                    ->searchable(),

                SelectFilter::make('vendor')
                    ->relationship('vendor', 'business_name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
