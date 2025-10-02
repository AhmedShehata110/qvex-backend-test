<?php

namespace App\Filament\Resources\Dashboard\Analytics;

use App\Filament\Resources\Dashboard\Analytics\Pages\CreateAnalytics;
use App\Filament\Resources\Dashboard\Analytics\Pages\EditAnalytics;
use App\Filament\Resources\Dashboard\Analytics\Pages\ListAnalytics;
use App\Filament\Resources\Dashboard\Analytics\Schemas\AnalyticsForm;
use App\Filament\Resources\Dashboard\Analytics\Tables\AnalyticsTable;
use App\Models\System\Analytics;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnalyticsResource extends Resource
{
    protected static ?string $model = Analytics::class;

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'metric';

    public static function form(Schema $schema): Schema
    {
        return AnalyticsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnalyticsTable::configure($table);
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
            'index' => ListAnalytics::route('/'),
            'create' => CreateAnalytics::route('/create'),
            'edit' => EditAnalytics::route('/{record}/edit'),
        ];
    }
}
