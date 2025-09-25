<?php

namespace App\Filament\Resources\Marketing\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('name_ar'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(['percentage' => 'Percentage', 'fixed_amount' => 'Fixed amount'])
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric(),
                TextInput::make('minimum_amount')
                    ->numeric(),
                TextInput::make('maximum_discount')
                    ->numeric(),
                TextInput::make('usage_limit')
                    ->numeric(),
                TextInput::make('usage_limit_per_user')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('applicable_to'),
                Toggle::make('is_active')
                    ->required(),
                Select::make('added_by_id')
                    ->relationship('addedBy', 'name'),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->required(),
            ]);
    }
}
