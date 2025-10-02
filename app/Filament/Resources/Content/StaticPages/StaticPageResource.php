<?php

namespace App\Filament\Resources\Content\StaticPages;

use App\Filament\Resources\Content\StaticPages\Pages\CreateStaticPage;
use App\Filament\Resources\Content\StaticPages\Pages\EditStaticPage;
use App\Filament\Resources\Content\StaticPages\Pages\ListStaticPages;
use App\Filament\Resources\Content\StaticPages\Schemas\StaticPageForm;
use App\Filament\Resources\Content\StaticPages\Tables\StaticPagesTable;
use App\Models\Content\StaticPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return StaticPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaticPagesTable::configure($table);
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
            'index' => ListStaticPages::route('/'),
            'create' => CreateStaticPage::route('/create'),
            'edit' => EditStaticPage::route('/{record}/edit'),
        ];
    }
}
