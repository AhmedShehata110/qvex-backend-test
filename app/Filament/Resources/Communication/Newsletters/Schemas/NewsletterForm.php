<?php

namespace App\Filament\Resources\Communication\Newsletters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->rules(['regex:/^[a-z0-9-]+$/']),

                        TextInput::make('subject')
                            ->label('Email Subject')
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                                'sent' => 'Sent',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'link',
                            ]),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Brief summary of the newsletter content'),
                    ]),

                Section::make('Template & Scheduling')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('template_id')
                                    ->label('Email Template')
                                    ->relationship('template', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select a template'),

                                DatePicker::make('scheduled_at')
                                    ->label('Schedule Send')
                                    ->placeholder('Leave empty to send immediately'),
                            ]),

                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Add tags...')
                            ->helperText('Tags for organizing newsletters'),
                    ]),

                Section::make('Performance Metrics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('recipient_count')
                                    ->label('Recipient Count')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('open_rate')
                                    ->label('Open Rate (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01),

                                TextInput::make('click_rate')
                                    ->label('Click Rate (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01),

                                DatePicker::make('sent_at')
                                    ->label('Sent At')
                                    ->placeholder('Not sent yet'),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        KeyValue::make('metadata')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addable()
                            ->deletable()
                            ->editableKeys()
                            ->editableValues(),
                    ])
                    ->collapsible(),
            ]);
    }
}
