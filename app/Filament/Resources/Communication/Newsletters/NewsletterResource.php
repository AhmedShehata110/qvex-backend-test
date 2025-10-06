<?php

namespace App\Filament\Resources\Communication\Newsletters;

use App\Filament\Resources\Communication\Newsletters\Pages\CreateNewsletter;
use App\Filament\Resources\Communication\Newsletters\Pages\EditNewsletter;
use App\Filament\Resources\Communication\Newsletters\Pages\ListNewsletters;
use App\Filament\Resources\Communication\Newsletters\Schemas\NewsletterForm;
use App\Filament\Resources\Communication\Newsletters\Tables\NewslettersTable;
use App\Models\Content\Newsletter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('keys.newsletters');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.communication');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return NewsletterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewslettersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
