<?php

namespace App\Filament\Resources\Marketing\Advertisements;

use App\Filament\Resources\Marketing\Advertisements\Pages\CreateAdvertisement;
use App\Filament\Resources\Marketing\Advertisements\Pages\EditAdvertisement;
use App\Filament\Resources\Marketing\Advertisements\Pages\ListAdvertisements;
use App\Filament\Resources\Marketing\Advertisements\Schemas\AdvertisementForm;
use App\Filament\Resources\Marketing\Advertisements\Tables\AdvertisementsTable;
use App\Models\Marketing\Advertisement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('keys.advertisements');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.marketing');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return AdvertisementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdvertisementsTable::configure($table);
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
            'index' => ListAdvertisements::route('/'),
            'create' => CreateAdvertisement::route('/create'),
            'edit' => EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
