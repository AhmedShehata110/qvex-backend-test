<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VendorSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.business_name')
                    ->label(__('keys.vendor'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('subscriptionPlan.name')
                    ->label(__('keys.subscription_plan'))
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'trial' => 'info',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('total_amount')
                    ->label(__('keys.amount_paid'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->label(__('keys.billing_cycle'))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),

                TextColumn::make('starts_at')
                    ->label(__('keys.start_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ends_at')
                    ->label(__('keys.end_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('next_billing_date')
                    ->label(__('keys.next_billing'))
                    ->date()
                    ->sortable()
                    ->placeholder('N/A'),

                IconColumn::make('auto_renewal')
                    ->label(__('keys.auto_renew'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('listings_used')
                    ->label(__('keys.listings_used'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
