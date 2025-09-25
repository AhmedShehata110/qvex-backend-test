<?php

namespace App\Filament\Resources\VehicleManagement\Colors;

use App\Filament\Resources\VehicleManagement\Colors\Pages\CreateColor;
use App\Filament\Resources\VehicleManagement\Colors\Pages\EditColor;
use App\Filament\Resources\VehicleManagement\Colors\Pages\ListColors;
use App\Filament\Resources\VehicleManagement\Colors\Schemas\ColorForm;
use App\Filament\Resources\VehicleManagement\Colors\Tables\ColorsTable;
use App\Models\Vehicle\Color;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;

    protected static string|UnitEnum|null $navigationGroup = 'Vehicle Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ColorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ColorsTable::configure($table);
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
            'index' => ListColors::route('/'),
            'create' => CreateColor::route('/create'),
            'edit' => EditColor::route('/{record}/edit'),
        ];
    }
}
