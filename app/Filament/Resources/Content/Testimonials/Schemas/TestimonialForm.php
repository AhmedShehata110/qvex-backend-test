<?php

namespace App\Filament\Resources\Content\Testimonials\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('keys.content_testimonials_full_name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('keys.content_testimonials_email_address'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('company')
                                    ->label(__('keys.company'))
                                    ->maxLength(255),

                                TextInput::make('position')
                                    ->label(__('keys.position_title'))
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Testimonial Content')
                    ->schema([
                        Textarea::make('content')
                            ->label(__('keys.testimonial'))
                            ->required()
                            ->maxLength(2000)
                            ->rows(6)
                            ->helperText('Customer\'s testimonial or review'),

                        Select::make('rating')
                            ->label(__('keys.rating'))
                            ->options([
                                1 => '1 Star',
                                2 => '2 Stars',
                                3 => '3 Stars',
                                4 => '4 Stars',
                                5 => '5 Stars',
                            ])
                            ->default(5)
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Publishing Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label(__('keys.testimonial'))
                                    ->default(false)
                                    ->helperText('Display prominently on the website'),

                                Toggle::make('is_approved')
                                    ->label(__('keys.content_testimonials_approved_for_display'))
                                    ->default(false)
                                    ->live()
                                    ->helperText('Make this testimonial visible to the public'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('approved_at')
                                    ->label(__('keys.content_testimonials_approved_date'))
                                    ->placeholder('Set approval date')
                                    ->visible(fn ($get) => $get('is_approved'))
                                    ->default(now()),

                                Select::make('approved_by')
                                    ->label(__('keys.content_testimonials_approved_by'))
                                    ->relationship('approver', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select approver')
                                    ->visible(fn ($get) => $get('is_approved')),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        KeyValue::make('metadata')
                            ->label(__('keys.metadata'))
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
