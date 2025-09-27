<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews\Schemas;

use App\Models\Communication\Review;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->description('Basic review information and relationships')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('reviewer_id')
                                    ->label('Reviewer')
                                    ->relationship('reviewer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('reviewee_id')
                                    ->label('Reviewee')
                                    ->relationship('reviewee', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('vehicle_id')
                                    ->label('Vehicle')
                                    ->relationship('vehicle', 'title')
                                    ->searchable()
                                    ->preload(),

                                Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('transaction_id')
                                    ->label('Transaction')
                                    ->relationship('transaction', 'transaction_number')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Section::make('Review Content')
                    ->description('Rating, title, and review content')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('rating')
                                    ->label('Rating')
                                    ->options([
                                        1 => '1 Star - Poor',
                                        2 => '2 Stars - Fair',
                                        3 => '3 Stars - Good',
                                        4 => '4 Stars - Very Good',
                                        5 => '5 Stars - Excellent',
                                    ])
                                    ->required(),

                                Toggle::make('would_recommend')
                                    ->label('Would Recommend')
                                    ->default(false),
                            ]),

                        TextInput::make('title')
                            ->label('Review Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label('Review Content')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Repeater::make('pros')
                                    ->label('Pros (Positive Points)')
                                    ->schema([
                                        TextInput::make('pro')
                                            ->label('Positive Point')
                                            ->maxLength(255)
                                            ->required(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Pro')
                                    ->collapsible()
                                    ->collapsed(),

                                Repeater::make('cons')
                                    ->label('Cons (Areas for Improvement)')
                                    ->schema([
                                        TextInput::make('con')
                                            ->label('Area for Improvement')
                                            ->maxLength(255)
                                            ->required(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Con')
                                    ->collapsible()
                                    ->collapsed(),
                            ]),
                    ]),

                Section::make('Review Status')
                    ->description('Review moderation and verification status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Review Status')
                                    ->options([
                                        Review::STATUS_PENDING => 'Pending Approval',
                                        Review::STATUS_APPROVED => 'Approved',
                                        Review::STATUS_REJECTED => 'Rejected',
                                        Review::STATUS_HIDDEN => 'Hidden',
                                    ])
                                    ->default(Review::STATUS_PENDING)
                                    ->required(),

                                Toggle::make('verified_purchase')
                                    ->label('Verified Purchase')
                                    ->default(false)
                                    ->helperText('Mark if this review is from a verified purchase'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('flagged_inappropriate')
                                    ->label('Flagged as Inappropriate')
                                    ->default(false)
                                    ->live(),

                                Textarea::make('flagged_reason')
                                    ->label('Flag Reason')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->visible(fn ($get) => $get('flagged_inappropriate')),
                            ]),
                    ]),
            ]);
    }
}
