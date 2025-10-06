<?php

namespace App\Filament\Resources\Marketing\Advertisements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdvertisementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('keys.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make('type')
                    ->label(__('keys.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'banner' => 'success',
                        'popup' => 'warning',
                        'sidebar' => 'info',
                        'email' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('position')
                    ->label(__('keys.position'))
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('keys.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('start_date')
                    ->label(__('keys.start_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('keys.end_date'))
                    ->date()
                    ->sortable()
                    ->placeholder('No end date'),

                TextColumn::make('click_count')
                    ->label(__('keys.click_count'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('view_count')
                    ->label(__('keys.views'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('budget')
                    ->label(__('keys.budget'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('spent')
                    ->label(__('keys.spent'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('keys.priority'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'danger',
                        $state >= 5 => 'warning',
                        $state >= 3 => 'info',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('keys.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('keys.type'))
                    ->options([
                        'banner' => 'Banner',
                        'popup' => 'Popup',
                        'sidebar' => 'Sidebar',
                        'email' => 'Email',
                    ])
                    ->multiple(),

                SelectFilter::make('position')
                    ->label(__('keys.position'))
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                        'sidebar' => 'Sidebar',
                        'content' => 'Content',
                        'popup' => 'Popup',
                    ])
                    ->multiple(),

                TernaryFilter::make('is_active')
                    ->label(__('keys.active')),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('start_date_from')
                            ->label(__('keys.start_date_from')),
                        DatePicker::make('start_date_to')
                            ->label(__('keys.start_date_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
