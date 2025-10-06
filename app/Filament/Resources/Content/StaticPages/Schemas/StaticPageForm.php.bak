<?php

namespace App\Filament\Resources\Content\StaticPages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaticPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Page Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Page Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->helperText('URL-friendly version of the title'),

                        Select::make('template')
                            ->label('Template')
                            ->options([
                                'default' => 'Default',
                                'full-width' => 'Full Width',
                                'sidebar' => 'With Sidebar',
                                'landing' => 'Landing Page',
                            ])
                            ->default('default')
                            ->required()
                            ->native(false),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Page Content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h1',
                                'h2',
                                'h3',
                                'h4',
                                'link',
                                'image',
                            ]),

                        Textarea::make('excerpt')
                            ->label('Excerpt/Summary')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Brief summary for SEO and previews'),
                    ]),

                Section::make('SEO & Publishing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->helperText('Title for search engines (60 chars max)'),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->maxLength(160)
                                    ->rows(2)
                                    ->helperText('Description for search engines (160 chars max)'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(false)
                                    ->live(),

                                DatePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->placeholder('Set publish date')
                                    ->visible(fn ($get) => $get('is_published'))
                                    ->default(now()),
                            ]),
                    ]),
            ]);
    }
}
