<?php

namespace App\Filament\Resources\Communication\NewsletterSubscribers;

use App\Filament\Resources\Communication\NewsletterSubscribers\Pages\CreateNewsletterSubscriber;
use App\Filament\Resources\Communication\NewsletterSubscribers\Pages\EditNewsletterSubscriber;
use App\Filament\Resources\Communication\NewsletterSubscribers\Pages\ListNewsletterSubscribers;
use App\Filament\Resources\Communication\NewsletterSubscribers\Schemas\NewsletterSubscriberForm;
use App\Filament\Resources\Communication\NewsletterSubscribers\Tables\NewsletterSubscribersTable;
use App\Models\Communication\NewsletterSubscriber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return NewsletterSubscriberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterSubscribersTable::configure($table);
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
            'index' => ListNewsletterSubscribers::route('/'),
            'create' => CreateNewsletterSubscriber::route('/create'),
            'edit' => EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
