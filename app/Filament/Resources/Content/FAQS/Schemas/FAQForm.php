<?php

namespace App\Filament\Resources\Content\FAQS\Schemas;

use App\Models\Content\FAQ;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FAQForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FAQ Content')
                    ->description('Question and answer content in multiple languages')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('question')
                                    ->label('Question (English)')
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TextInput::make('question_ar')
                                    ->label('Question (Arabic)')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),

                        RichEditor::make('answer')
                            ->label('Answer (English)')
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        RichEditor::make('answer_ar')
                            ->label('Answer (Arabic)')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),

                Section::make('FAQ Organization')
                    ->description('Category, tags, and organization settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        FAQ::CATEGORY_GENERAL => 'General Questions',
                                        FAQ::CATEGORY_BUYING => 'Buying Vehicles',
                                        FAQ::CATEGORY_SELLING => 'Selling Vehicles',
                                        FAQ::CATEGORY_RENTAL => 'Vehicle Rental',
                                        FAQ::CATEGORY_PAYMENTS => 'Payments & Pricing',
                                        FAQ::CATEGORY_ACCOUNT => 'Account Management',
                                        FAQ::CATEGORY_TECHNICAL => 'Technical Support',
                                    ])
                                    ->required()
                                    ->default(FAQ::CATEGORY_GENERAL),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                            ]),

                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Add relevant tags')
                            ->helperText('Press Enter to add each tag')
                            ->columnSpanFull(),
                    ]),

                Section::make('Publication Settings')
                    ->description('Status and visibility settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Publication Status')
                                    ->options([
                                        FAQ::STATUS_DRAFT => 'Draft',
                                        FAQ::STATUS_PUBLISHED => 'Published',
                                        FAQ::STATUS_ARCHIVED => 'Archived',
                                    ])
                                    ->required()
                                    ->default(FAQ::STATUS_DRAFT),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Only active FAQs are shown publicly'),
                            ]),

                        Select::make('added_by_id')
                            ->label('Author')
                            ->relationship('addedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->disabled(fn ($context) => $context === 'edit'),
                    ]),
            ]);
    }
}
