<?php

namespace App\Filament\Resources\Content\EmailTemplates;

use App\Filament\Resources\Content\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\Content\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\Content\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\Content\EmailTemplates\Schemas\EmailTemplateForm;
use App\Filament\Resources\Content\EmailTemplates\Tables\EmailTemplatesTable;
use App\Models\Marketing\EmailTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EmailTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTemplatesTable::configure($table);
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
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
