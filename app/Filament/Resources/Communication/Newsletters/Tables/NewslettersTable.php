<?php

namespace App\Filament\Resources\Communication\Newsletters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewslettersTable
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

                TextColumn::make('subject')
                    ->label(__('keys.email_subject'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make('status')
                    ->label(__('keys.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'scheduled' => 'warning',
                        'draft' => 'gray',
                        'sent' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('template.title')
                    ->label(__('keys.email_template'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('No template'),

                TextColumn::make('scheduled_at')
                    ->label(__('keys.schedule_send'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not scheduled'),

                TextColumn::make('sent_at')
                    ->label(__('keys.sent_at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not sent'),

                TextColumn::make('recipient_count')
                    ->label(__('keys.recipient_count'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('open_rate')
                    ->label(__('keys.open_rate'))
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('click_rate')
                    ->label(__('keys.click_rate'))
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

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
                SelectFilter::make('status')
                    ->label(__('keys.status'))
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'sent' => 'Sent',
                    ])
                    ->multiple(),

                SelectFilter::make('template_id')
                    ->label(__('keys.template'))
                    ->relationship('template', 'title')
                    ->searchable()
                    ->preload(),

                Filter::make('scheduled_date')
                    ->form([
                        DatePicker::make('scheduled_from')
                            ->label(__('keys.scheduled_from')),
                        DatePicker::make('scheduled_to')
                            ->label(__('keys.scheduled_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    }),

                Filter::make('sent_date')
                    ->form([
                        DatePicker::make('sent_from')
                            ->label(__('keys.sent_from')),
                        DatePicker::make('sent_to')
                            ->label(__('keys.sent_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['sent_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '>=', $date),
                            )
                            ->when(
                                $data['sent_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '<=', $date),
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
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
