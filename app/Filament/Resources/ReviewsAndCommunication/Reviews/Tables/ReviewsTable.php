<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Tables;

use App\Models\Communication\Review;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
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
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state).' ('.$state.'/5)')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger'
                    }),

                TextColumn::make('title')
                    ->label('Review Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'warning' => Review::STATUS_PENDING,
                        'success' => Review::STATUS_APPROVED,
                        'danger' => Review::STATUS_REJECTED,
                        'gray' => Review::STATUS_HIDDEN,
                    ]),

                IconColumn::make('verified_purchase')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('would_recommend')
                    ->label('Recommends')
                    ->boolean()
                    ->trueIcon('heroicon-o-hand-thumb-up')
                    ->falseIcon('heroicon-o-hand-thumb-down')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

                IconColumn::make('flagged_inappropriate')
                    ->label('Flagged')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('not_helpful_count')
                    ->label('Not Helpful')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('response_count')
                    ->label('Responses')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('content')
                    ->label('Review Content')
                    ->limit(100)
                    ->tooltip(fn ($record) => $record->content)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not approved')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
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
                    ->label('Verified Purchase')
                    ->placeholder('All reviews')
                    ->trueLabel('Verified purchases')
                    ->falseLabel('Non-verified purchases'),

                TernaryFilter::make('would_recommend')
                    ->label('Recommendation')
                    ->placeholder('All reviews')
                    ->trueLabel('Would recommend')
                    ->falseLabel('Would not recommend'),

                TernaryFilter::make('flagged_inappropriate')
                    ->label('Flagged Content')
                    ->placeholder('All reviews')
                    ->trueLabel('Flagged reviews')
                    ->falseLabel('Clean reviews'),

                SelectFilter::make('reviewer')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('vendor')
                    ->relationship('vendor', 'name')
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
