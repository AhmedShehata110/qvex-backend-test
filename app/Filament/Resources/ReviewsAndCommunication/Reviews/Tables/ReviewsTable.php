<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Tables;

use App\Models\Communication\Review;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rating')
                    ->label(__('keys.rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state).' ('.$state.'/5)')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger'
                    }),

                TextColumn::make('title')
                    ->label(__('keys.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('reviewer.name')
                    ->label(__('keys.reviewer'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('vehicle.title')
                    ->label(__('keys.vehicle'))
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('vendor.name')
                    ->label(__('keys.vendor'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('keys.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'warning' => Review::STATUS_PENDING,
                        'success' => Review::STATUS_APPROVED,
                        'danger' => Review::STATUS_REJECTED,
                        'gray' => Review::STATUS_HIDDEN,
                    ]),

                IconColumn::make('verified_purchase')
                    ->label(__('keys.verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('would_recommend')
                    ->label(__('keys.recommends'))
                    ->boolean()
                    ->trueIcon('heroicon-o-hand-thumb-up')
                    ->falseIcon('heroicon-o-hand-thumb-down')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

                IconColumn::make('flagged_inappropriate')
                    ->label(__('keys.flagged'))
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('helpful_count')
                    ->label(__('keys.helpful'))
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('not_helpful_count')
                    ->label(__('keys.not_helpful'))
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('response_count')
                    ->label(__('keys.responses'))
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('content')
                    ->label(__('keys.content'))
                    ->limit(100)
                    ->tooltip(fn ($record) => $record->content)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label(__('keys.approved'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not approved')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->options([
                        '5' => '5 Stars',
                        '4' => '4 Stars',
                        '3' => '3 Stars',
                        '2' => '2 Stars',
                        '1' => '1 Star',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        Review::STATUS_PENDING => 'Pending Approval',
                        Review::STATUS_APPROVED => 'Approved',
                        Review::STATUS_REJECTED => 'Rejected',
                        Review::STATUS_HIDDEN => 'Hidden',
                    ]),

                TernaryFilter::make('verified_purchase')
                    ->label(__('keys.verified_purchase'))
                    ->placeholder('All reviews')
                    ->trueLabel('Verified purchases')
                    ->falseLabel('Non-verified purchases'),

                TernaryFilter::make('would_recommend')
                    ->label(__('keys.recommendation'))
                    ->placeholder('All reviews')
                    ->trueLabel('Would recommend')
                    ->falseLabel('Would not recommend'),

                TernaryFilter::make('flagged_inappropriate')
                    ->label(__('keys.content'))
                    ->placeholder('All reviews')
                    ->trueLabel('Flagged reviews')
                    ->falseLabel('Clean reviews'),

                SelectFilter::make('reviewer')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('vendor')
                    ->relationship('vendor', 'business_name')
                    ->searchable()
                    ->preload(),
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
