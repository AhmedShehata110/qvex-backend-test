<?php

namespace App\Filament\Resources\Dashboard\Analytics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnalyticsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'page_view' => 'success',
                        'user_action' => 'info',
                        'conversion' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('entity_type')
                    ->label('Entity Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('entity_id')
                    ->label('Entity ID')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('metric')
                    ->label('Metric')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

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
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'page_view' => 'Page View',
                        'user_action' => 'User Action',
                        'conversion' => 'Conversion',
                        'error' => 'Error',
                    ])
                    ->multiple(),

                SelectFilter::make('entity_type')
                    ->label('Entity Type')
                    ->options([
                        'vehicle' => 'Vehicle',
                        'user' => 'User',
                        'vendor' => 'Vendor',
                        'page' => 'Page',
                    ])
                    ->multiple(),

                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Date From'),
                        DatePicker::make('date_to')
                            ->label('Date To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
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
            ->defaultSort('date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
