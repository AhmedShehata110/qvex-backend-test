<?php

namespace App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Plan Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('price_monthly')
                    ->label('Monthly Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('price_yearly')
                    ->label('Yearly Price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'monthly' => 'info',
                        'yearly' => 'success',
                        'lifetime' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('trial_days')
                    ->label('Trial Days')
                    ->numeric()
                    ->sortable()
                    ->placeholder('No trial'),

                TextColumn::make('vehicle_listing_limit')
                    ->label('Vehicle Listings')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Unlimited'),

                TextColumn::make('staff_account_limit')
                    ->label('Staff Accounts')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Unlimited')
                    ->toggleable(),

                TextColumn::make('featured_listing_limit')
                    ->label('Featured Listings')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Unlimited')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),

                TextColumn::make('subscriptions_count')
                    ->label('Active Subscriptions')
                    ->counts('subscriptions')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All plans')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                TernaryFilter::make('is_popular')
                    ->label('Popular Plans')
                    ->placeholder('All plans')
                    ->trueLabel('Popular only')
                    ->falseLabel('Regular only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
