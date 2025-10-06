<?php

namespace App\Filament\Resources\Content\StaticPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StaticPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('keys.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make('slug')
                    ->label(__('keys.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Slug copied!')
                    ->copyMessageDuration(1500),

                IconColumn::make('is_published')
                    ->label(__('keys.published'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('published_at')
                    ->label(__('keys.published_at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not published'),

                TextColumn::make('template')
                    ->label(__('keys.template'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'gray',
                        'full-width' => 'info',
                        'sidebar' => 'warning',
                        'landing' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('keys.order'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('meta_title')
                    ->label(__('keys.meta_title'))
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('keys.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label(__('keys.published')),

                SelectFilter::make('template')
                    ->label(__('keys.template'))
                    ->options([
                        'default' => 'Default',
                        'full-width' => 'Full Width',
                        'sidebar' => 'With Sidebar',
                        'landing' => 'Landing Page',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->paginated([10, 25, 50, 100]);
    }
}
