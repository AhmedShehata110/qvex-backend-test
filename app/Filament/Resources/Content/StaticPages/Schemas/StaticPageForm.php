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
                            ->label(__('keys.title'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label(__('keys.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->helperText('URL-friendly version of the title'),

                        Select::make('template')
                            ->label(__('keys.template'))
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
                            ->label(__('keys.content_staticpages_display_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label(__('keys.content'))
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
                            ->label(__('keys.content_staticpages_excerpt_summary'))
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Brief summary for SEO and previews'),
                    ]),

                Section::make('SEO & Publishing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('keys.content_staticpages_meta_title'))
                                    ->maxLength(60)
                                    ->helperText('Title for search engines (60 chars max)'),

                                Textarea::make('meta_description')
                                    ->label(__('keys.content_staticpages_meta_description'))
                                    ->maxLength(160)
                                    ->rows(2)
                                    ->helperText('Description for search engines (160 chars max)'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label(__('keys.content_staticpages_published'))
                                    ->default(false)
                                    ->live(),

                                DatePicker::make('published_at')
                                    ->label(__('keys.content_staticpages_publish_date'))
                                    ->placeholder('Set publish date')
                                    ->visible(fn ($get) => $get('is_published'))
                                    ->default(now()),
                            ]),
                    ]),
            ]);
    }
}
