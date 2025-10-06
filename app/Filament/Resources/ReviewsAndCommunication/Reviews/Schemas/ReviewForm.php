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
                    ->description(__('keys.basic_review_information_and_relationships'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('reviewer_id')
                                    ->label(__('keys.reviewer'))
                                    ->relationship('reviewer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('reviewee_id')
                                    ->label(__('keys.reviewee'))
                                    ->relationship('reviewee', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('vehicle_id')
                                    ->label(__('keys.vehicle'))
                                    ->relationship('vehicle', 'title')
                                    ->searchable()
                                    ->preload(),

                                Select::make('vendor_id')
                                    ->label(__('keys.vendor'))
                                    ->relationship('vendor', 'business_name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('transaction_id')
                                    ->label(__('keys.transaction'))
                                    ->relationship('transaction', 'transaction_number')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Section::make('Review Content')
                    ->description(__('keys.rating_title_and_review_content'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('rating')
                                    ->label(__('keys.rating'))
                                    ->options([
                                        1 => '1 Star - Poor',
                                        2 => '2 Stars - Fair',
                                        3 => '3 Stars - Good',
                                        4 => '4 Stars - Very Good',
                                        5 => '5 Stars - Excellent',
                                    ])
                                    ->required(),

                                Toggle::make('would_recommend')
                                    ->label(__('keys.would_recommend'))
                                    ->default(false),
                            ]),

                        TextInput::make('title')
                            ->label(__('keys.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label(__('keys.content'))
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Repeater::make('pros')
                                    ->label(__('keys.pros'))
                                    ->schema([
                                        TextInput::make('pro')
                                            ->label(__('keys.positive_point'))
                                            ->maxLength(255)
                                            ->required(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Pro')
                                    ->collapsible()
                                    ->collapsed(),

                                Repeater::make('cons')
                                    ->label(__('keys.cons'))
                                    ->schema([
                                        TextInput::make('con')
                                            ->label(__('keys.area_for_improvement'))
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
                    ->description(__('keys.review_moderation_and_verification_status'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label(__('keys.status'))
                                    ->options([
                                        Review::STATUS_PENDING => 'Pending Approval',
                                        Review::STATUS_APPROVED => 'Approved',
                                        Review::STATUS_REJECTED => 'Rejected',
                                        Review::STATUS_HIDDEN => 'Hidden',
                                    ])
                                    ->default(Review::STATUS_PENDING)
                                    ->required(),

                                Toggle::make('verified_purchase')
                                    ->label(__('keys.verified_purchase'))
                                    ->default(false)
                                    ->helperText('Mark if this review is from a verified purchase'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('flagged_inappropriate')
                                    ->label(__('keys.flagged_as_inappropriate'))
                                    ->default(false)
                                    ->live(),

                                Textarea::make('flagged_reason')
                                    ->label(__('keys.flag_reason'))
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->visible(fn ($get) => $get('flagged_inappropriate')),
                            ]),
                    ]),
            ]);
    }
}
