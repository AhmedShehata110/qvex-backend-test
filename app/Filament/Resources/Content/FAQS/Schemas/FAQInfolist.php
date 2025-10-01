<?php

namespace App\Filament\Resources\Content\FAQS\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FAQInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('question'),
                TextEntry::make('question_ar'),
                TextEntry::make('answer'),
                TextEntry::make('answer_ar'),
                TextEntry::make('category'),
                TextEntry::make('sort_order'),
                TextEntry::make('view_count'),
                TextEntry::make('helpful_count'),
                TextEntry::make('not_helpful_count'),
                KeyValueEntry::make('tags'),
                TextEntry::make('status'),
                TextEntry::make('is_active'),
                TextEntry::make('added_by.name'),
            ]);
    }
}
