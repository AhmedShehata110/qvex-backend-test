<?php

namespace App\Filament\Resources\Locations\Branches;

use App\Filament\Resources\Locations\Branches\Pages\CreateBranches;
use App\Filament\Resources\Locations\Branches\Pages\EditBranches;
use App\Filament\Resources\Locations\Branches\Pages\ListBranches;
use App\Filament\Resources\Locations\Branches\Schemas\BranchesForm;
use App\Filament\Resources\Locations\Branches\Tables\BranchesTable;
use App\Models\Location\Branch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BranchesResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('keys.branches');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.locations');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BranchesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
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
            'index' => ListBranches::route('/'),
            'create' => CreateBranches::route('/create'),
            'edit' => EditBranches::route('/{record}/edit'),
        ];
    }
}
