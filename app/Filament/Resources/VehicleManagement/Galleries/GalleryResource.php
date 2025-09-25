<?php

namespace App\Filament\Resources\VehicleManagement\Galleries;

use App\Filament\Resources\VehicleManagement\Galleries\Pages\CreateGallery;
use App\Filament\Resources\VehicleManagement\Galleries\Pages\EditGallery;
use App\Filament\Resources\VehicleManagement\Galleries\Pages\ListGalleries;
use App\Filament\Resources\VehicleManagement\Galleries\Schemas\GalleryForm;
use App\Filament\Resources\VehicleManagement\Galleries\Tables\GalleriesTable;
use App\Models\Vehicle\Gallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string|UnitEnum|null $navigationGroup = 'Vehicle Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleriesTable::configure($table);
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
            'index' => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit' => EditGallery::route('/{record}/edit'),
        ];
    }
}
