<?php

namespace App\Filament\Resources\Content\FAQS\Tables;

use App\Models\Content\FAQ;
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

class FAQSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->sortable()
                    ->limit(80)
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        FAQ::CATEGORY_GENERAL => 'General',
                        FAQ::CATEGORY_BUYING => 'Buying',
                        FAQ::CATEGORY_SELLING => 'Selling',
                        FAQ::CATEGORY_RENTAL => 'Rental',
                        FAQ::CATEGORY_PAYMENTS => 'Payments',
                        FAQ::CATEGORY_ACCOUNT => 'Account',
                        FAQ::CATEGORY_TECHNICAL => 'Technical',
                        default => ucfirst($state)
                    })
                    ->colors([
                        'primary' => FAQ::CATEGORY_GENERAL,
                        'success' => [FAQ::CATEGORY_BUYING, FAQ::CATEGORY_SELLING],
                        'warning' => FAQ::CATEGORY_RENTAL,
                        'info' => [FAQ::CATEGORY_PAYMENTS, FAQ::CATEGORY_ACCOUNT],
                        'danger' => FAQ::CATEGORY_TECHNICAL,
                    ]),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'gray' => FAQ::STATUS_DRAFT,
                        'success' => FAQ::STATUS_PUBLISHED,
                        'warning' => FAQ::STATUS_ARCHIVED,
                    ]),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('not_helpful_count')
                    ->label('Not Helpful')
                    ->formatStateUsing(fn ($state) => number_format($state ?: 0))
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('helpfulness_ratio')
                    ->label('Helpfulness')
                    ->state(function ($record) {
                        $total = ($record->helpful_count ?: 0) + ($record->not_helpful_count ?: 0);
                        if ($total === 0) {
                            return 'N/A';
                        }
                        $ratio = round((($record->helpful_count ?: 0) / $total) * 100, 1);

                        return $ratio.'%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $total = ($record->helpful_count ?: 0) + ($record->not_helpful_count ?: 0);
                        if ($total === 0) {
                            return 'gray';
                        }
                        $ratio = (($record->helpful_count ?: 0) / $total) * 100;

                        return match (true) {
                            $ratio >= 80 => 'success',
                            $ratio >= 60 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->toggleable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : '')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('addedBy.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        FAQ::CATEGORY_GENERAL => 'General Questions',
                        FAQ::CATEGORY_BUYING => 'Buying Vehicles',
                        FAQ::CATEGORY_SELLING => 'Selling Vehicles',
                        FAQ::CATEGORY_RENTAL => 'Vehicle Rental',
                        FAQ::CATEGORY_PAYMENTS => 'Payments & Pricing',
                        FAQ::CATEGORY_ACCOUNT => 'Account Management',
                        FAQ::CATEGORY_TECHNICAL => 'Technical Support',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        FAQ::STATUS_DRAFT => 'Draft',
                        FAQ::STATUS_PUBLISHED => 'Published',
                        FAQ::STATUS_ARCHIVED => 'Archived',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All FAQs')
                    ->trueLabel('Active FAQs')
                    ->falseLabel('Inactive FAQs'),

                SelectFilter::make('addedBy')
                    ->label('Author')
                    ->relationship('addedBy', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}
