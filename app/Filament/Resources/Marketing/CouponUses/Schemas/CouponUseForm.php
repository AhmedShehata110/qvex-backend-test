<?php

namespace App\Filament\Resources\Marketing\CouponUses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CouponUseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('coupon_id')
                    ->relationship('coupon', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('transaction_id')
                    ->relationship('transaction', 'id')
                    ->required(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric(),
            ]);
    }
}
