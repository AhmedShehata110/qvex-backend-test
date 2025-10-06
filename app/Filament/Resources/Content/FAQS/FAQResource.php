<?php

namespace App\Filament\Resources\Content\FAQS;

use App\Filament\Resources\Content\FAQS\Pages\CreateFAQ;
use App\Filament\Resources\Content\FAQS\Pages\EditFAQ;
use App\Filament\Resources\Content\FAQS\Pages\ListFAQS;
use App\Filament\Resources\Content\FAQS\Pages\ViewFAQ;
use App\Filament\Resources\Content\FAQS\Schemas\FAQForm;
use App\Filament\Resources\Content\FAQS\Schemas\FAQInfolist;
use App\Filament\Resources\Content\FAQS\Tables\FAQSTable;
use App\Models\Content\FAQ;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FAQResource extends Resource
{
    protected static ?string $model = FAQ::class;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.faqs');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.content');
    }

    protected static ?string $recordTitleAttribute = 'question';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return FAQForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FAQInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FAQSTable::configure($table);
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
            'index' => ListFAQS::route('/'),
            'create' => CreateFAQ::route('/create'),
            'view' => ViewFAQ::route('/{record}'),
            'edit' => EditFAQ::route('/{record}/edit'),
        ];
    }
}
