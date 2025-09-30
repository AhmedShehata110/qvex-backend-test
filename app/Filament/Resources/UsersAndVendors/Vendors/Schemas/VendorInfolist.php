<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Vendor Information')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('business_name')
                                    ->label('Vendor Name')
                                    ->size(TextSize::Large)
                                    ->weight('bold'),

                                TextEntry::make('slug')
                                    ->label('URL Slug')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('owner.email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable(),

                                TextEntry::make('owner.phone')
                                    ->label('Phone')
                                    ->icon('heroicon-m-phone')
                                    ->copyable(),

                                TextEntry::make('website')
                                    ->label('Website')
                                    ->icon('heroicon-m-globe-alt')
                                    ->url(fn ($record) => $record->website)
                                    ->openUrlInNewTab()
                                    ->placeholder('No website'),

                                TextEntry::make('registration_number')
                                    ->label('Registration Number')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                TextEntry::make('tax_id')
                                    ->label('Tax ID')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                TextEntry::make('trade_license')
                                    ->label('Trade License')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                TextEntry::make('business_name_ar')
                                    ->label('Business Name (Arabic)')
                                    ->placeholder('Not provided'),

                                TextEntry::make('services_offered')
                                    ->label('Services Offered')
                                    ->state(function ($record) {
                                        if (! $record->services_offered) {
                                            return null;
                                        }

                                        $services = is_array($record->services_offered)
                                            ? $record->services_offered
                                            : [$record->services_offered];

                                        // Convert snake_case to readable and return as array for badges
                                        return array_map(function ($service) {
                                            return ucwords(str_replace('_', ' ', $service));
                                        }, $services);
                                    })
                                    ->badge()
                                    ->color('info')
                                    ->separator(' ')
                                    ->placeholder('No services specified')
                                    ->columnSpanFull(),

                                KeyValueEntry::make('business_hours')
                                    ->label('Business Hours')
                                    ->keyLabel('Day')
                                    ->valueLabel('Operating Hours')
                                    ->state(function ($record) {
                                        return static::formatBusinessHours($record->business_hours);
                                    })
                                    ->columnSpanFull(),

                            ]),

                        Section::make('Vendor Details')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('owner.name')
                                    ->label('Owner')
                                    ->icon('heroicon-m-user')
                                    ->placeholder('No owner assigned'),

                                TextEntry::make('vendor_type')
                                    ->label('Vendor Type')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('status')
                                    ->label('Account Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'suspended' => 'danger',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('commission_rate')
                                    ->label('Commission Rate')
                                    ->suffix('%')
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('total_sales')
                                    ->label('Total Sales')
                                    ->numeric(),

                                TextEntry::make('total_revenue')
                                    ->label('Total Revenue')
                                    ->money('USD')
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('rating_average')
                                    ->label('Average Rating')
                                    ->numeric(decimalPlaces: 1)
                                    ->suffix('/5')
                                    ->placeholder('No ratings yet'),

                                TextEntry::make('rating_count')
                                    ->label('Total Reviews')
                                    ->numeric()
                                    ->placeholder('0'),

                                IconEntry::make('is_featured')
                                    ->label('Featured Vendor')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus')
                                    ->trueColor('warning')
                                    ->falseColor('gray'),

                                IconEntry::make('is_verified')
                                    ->label('Verified')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-check')
                                    ->falseIcon('heroicon-o-shield-exclamation')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('verified_at')
                                    ->label('Verified At')
                                    ->dateTime()
                                    ->placeholder('Not verified'),

                                IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('vehicles_count')
                                    ->label('Total Vehicles')
                                    ->state(fn ($record) => $record->vehicles()->count())
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('active_vehicles_count')
                                    ->label('Active Vehicles')
                                    ->state(fn ($record) => $record->vehicles()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success'),
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Created')
                                            ->dateTime(),

                                        TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->dateTime(),

                                        TextEntry::make('deleted_at')
                                            ->label('Deleted')
                                            ->dateTime()
                                            ->placeholder('Not deleted'),
                                    ]),
                            ])
                            ->collapsible(),
                    ])->columnSpanFull(),

            ]);
    }

    protected static function formatBusinessHours($hours): array
    {
        if (! $hours || ! is_array($hours) || empty($hours)) {
            return ['Status' => 'Business hours not specified'];
        }

        $formatted = [];
        $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $today = strtolower(now()->format('l')); // Get current day

        foreach ($dayOrder as $day) {
            if (isset($hours[$day])) {
                $dayData = $hours[$day];
                $dayName = ucfirst($day);

                // Highlight today
                if ($day === $today) {
                    $dayName = "🔵 {$dayName} (Today)";
                }
                // Handle different data structures
                if (is_array($dayData)) {
                    // Check if closed
                    if (isset($dayData['is_open']) && ($dayData['is_open'] === false || $dayData['is_open'] === 'false')) {
                        $formatted[$dayName] = '🔒 Closed';
                    } else {
                        // Get open and close times
                        $openTime = static::formatTime($dayData['open'] ?? $dayData['start'] ?? ($dayData[0] ?? null));
                        $closeTime = static::formatTime($dayData['close'] ?? $dayData['end'] ?? ($dayData[1] ?? null));

                        if ($openTime && $closeTime) {
                            // Check if currently open (only for today)
                            $status = ($day === $today && static::isCurrentlyOpen($openTime, $closeTime)) ? '🟢' : '🕐';
                            $formatted[$dayName] = "{$status} {$openTime} - {$closeTime}";
                        } else {
                            $formatted[$dayName] = '❓ Hours not specified';
                        }
                    }
                } else {
                    // Handle string or other formats
                    $formatted[$dayName] = (string) $dayData;
                }
            }
        }

        return $formatted;
    }

    protected static function formatTime(?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        try {
            // Try to parse and format time to 12-hour format
            $carbonTime = \Carbon\Carbon::createFromFormat('H:i', $time);

            return $carbonTime->format('g:i A');
        } catch (\Exception $e) {
            try {
                // Try alternative format
                $carbonTime = \Carbon\Carbon::createFromFormat('H:i:s', $time);

                return $carbonTime->format('g:i A');
            } catch (\Exception $e) {
                // If parsing fails, return as-is
                return $time;
            }
        }
    }

    /**
     * Check if currently open (for today only)
     */
    protected static function isCurrentlyOpen(string $openTime, string $closeTime): bool
    {
        try {
            $currentTime = now()->format('H:i');
            $open = \Carbon\Carbon::createFromFormat('g:i A', $openTime)->format('H:i');
            $close = \Carbon\Carbon::createFromFormat('g:i A', $closeTime)->format('H:i');

            // Handle cases where close time is before open time (crosses midnight)
            if ($close < $open) {
                return $currentTime >= $open || $currentTime <= $close;
            }

            return $currentTime >= $open && $currentTime <= $close;
        } catch (\Exception $e) {
            return false;
        }
    }
}
