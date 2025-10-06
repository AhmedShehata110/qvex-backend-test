<?php

namespace App\Filament\Resources\SalesAndTransactions\Carts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cart Information')
                    ->description('Basic cart details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('User')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('The user who owns this cart'),

                                TextInput::make('session_id')
                                    ->label('Session ID')
                                    ->maxLength(255)
                                    ->helperText('Session identifier for guest carts'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_items')
                                    ->label('Total Items')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Total number of items in cart'),

                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0)
                                    ->helperText('Total cart amount'),

                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        'USD' => 'USD',
                                        'EUR' => 'EUR',
                                        'GBP' => 'GBP',
                                        'CAD' => 'CAD',
                                        'AUD' => 'AUD',
                                    ])
                                    ->default('USD')
                                    ->helperText('Cart currency'),
                            ]),
                    ]),

                Section::make('Cart Status & Settings')
                    ->description('Cart status and expiration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'abandoned' => 'Abandoned',
                                        'converted' => 'Converted to Order',
                                        'expired' => 'Expired',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->helperText('Current cart status'),

                                DateTimePicker::make('expires_at')
                                    ->label('Expires At')
                                    ->helperText('When the cart expires (optional)'),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->description('Metadata and additional details')
                    ->schema([
                        Textarea::make('metadata')
                            ->label('Metadata')
                            ->rows(4)
                            ->json()
                            ->helperText('Additional cart data (JSON format)'),
                    ]),
            ]);
    }
}
