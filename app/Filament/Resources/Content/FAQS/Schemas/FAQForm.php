<?php

namespace App\Filament\Resources\Content\FAQS\Schemas;

use App\Models\Content\FAQ;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
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
                    ->description(__('keys.content_faqs_question_and_answer_content_in_multiple_languages'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('question')
                                    ->label(__('keys.question'))
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TextInput::make('question_ar')
                                    ->label(__('keys.content_faqs_question_1'))
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),

                        RichEditor::make('answer')
                            ->label(__('keys.answer'))
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        RichEditor::make('answer_ar')
                            ->label(__('keys.content_faqs_answer_1'))
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),

                Section::make('FAQ Organization')
                    ->description(__('keys.content_faqs_category_tags_and_organization_settings'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label(__('keys.category'))
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
                                    ->label(__('keys.content_faqs_sort_order'))
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                            ]),

                        TagsInput::make('tags')
                            ->label(__('keys.tags'))
                            ->placeholder('Add relevant tags')
                            ->helperText('Press Enter to add each tag')
                            ->columnSpanFull(),
                    ]),

                Section::make('Publication Settings')
                    ->description(__('keys.content_faqs_status_and_visibility_settings'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label(__('keys.content_faqs_publication_status'))
                                    ->options([
                                        FAQ::STATUS_DRAFT => 'Draft',
                                        FAQ::STATUS_PUBLISHED => 'Published',
                                        FAQ::STATUS_ARCHIVED => 'Archived',
                                    ])
                                    ->required()
                                    ->default(FAQ::STATUS_DRAFT),

                                Toggle::make('is_active')
                                    ->label(__('keys.active'))
                                    ->default(true)
                                    ->helperText('Only active FAQs are shown publicly'),
                            ]),

                        Select::make('added_by_id')
                            ->label(__('keys.content_faqs_author'))
                            ->relationship('addedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->disabled(fn ($context) => $context === 'edit'),
                    ]),
            ]);
    }
}
