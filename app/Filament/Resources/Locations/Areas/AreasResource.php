<?php

namespace App\Filament\Resources\Locations\Areas;

use App\Filament\Resources\Locations\Areas\Pages\CreateAreas;
use App\Filament\Resources\Locations\Areas\Pages\EditAreas;
use App\Filament\Resources\Locations\Areas\Pages\ListAreas;
use App\Filament\Resources\Locations\Areas\Schemas\AreasForm;
use App\Filament\Resources\Locations\Areas\Tables\AreasTable;
use App\Models\Location\Area;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AreasResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AreasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreasTable::configure($table);
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
            'index' => ListAreas::route('/'),
            'create' => CreateAreas::route('/create'),
            'edit' => EditAreas::route('/{record}/edit'),
        ];
    }
}
