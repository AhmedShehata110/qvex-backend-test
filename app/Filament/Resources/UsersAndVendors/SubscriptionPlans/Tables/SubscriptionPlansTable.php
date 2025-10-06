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
                    ->label(__('keys.plan_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('price_monthly')
                    ->label(__('keys.price'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('price_yearly')
                    ->label(__('keys.price'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('billing_cycle')
                    ->label(__('keys.billing_cycle'))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'monthly' => 'info',
                        'yearly' => 'success',
                        'lifetime' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('trial_days')
                    ->label(__('keys.trial_days'))
                    ->numeric()
                    ->sortable()
                    ->placeholder(__('keys.no_trial')),

                TextColumn::make('vehicle_listing_limit')
                    ->label(__('keys.vehicle_listings'))
                    ->numeric()
                    ->sortable()
                    ->placeholder(__('keys.unlimited')),

                TextColumn::make('staff_account_limit')
                    ->label(__('keys.staff_accounts'))
                    ->numeric()
                    ->sortable()
                    ->placeholder(__('keys.unlimited'))
                    ->toggleable(),

                TextColumn::make('featured_listing_limit')
                    ->label(__('keys.featured_listings'))
                    ->numeric()
                    ->sortable()
                    ->placeholder('Unlimited')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('keys.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_popular')
                    ->label(__('keys.popular'))
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),

                TextColumn::make('subscriptions_count')
                    ->label(__('keys.active_subscriptions'))
                    ->counts('subscriptions')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('keys.sort_order'))
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
                TernaryFilter::make('is_active')
                    ->label(__('keys.status'))
                    ->placeholder(__('keys.all_plans'))
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                TernaryFilter::make('is_popular')
                    ->label(__('keys.popular_plan'))
                    ->placeholder(__('keys.all_plans'))
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
