<?php

namespace App\Filament\Resources\ReviewsAndCommunication\ContactUs;

use App\Filament\Resources\ReviewsAndCommunication\ContactUs\Pages\CreateContactUs;
use App\Filament\Resources\ReviewsAndCommunication\ContactUs\Pages\EditContactUs;
use App\Filament\Resources\ReviewsAndCommunication\ContactUs\Pages\ListContactUs;
use App\Filament\Resources\ReviewsAndCommunication\ContactUs\Schemas\ContactUsForm;
use App\Filament\Resources\ReviewsAndCommunication\ContactUs\Tables\ContactUsTable;
use App\Models\Communication\ContactUs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reviews & Communication';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('keys.contact_us');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.reviews_communication');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ContactUsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactUsTable::configure($table);
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
            'index' => ListContactUs::route('/'),
            'create' => CreateContactUs::route('/create'),
            'edit' => EditContactUs::route('/{record}/edit'),
        ];
    }
}
