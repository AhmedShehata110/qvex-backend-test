<?php

namespace App\Filament\Resources\Marketing\CouponUses;

use App\Filament\Resources\Marketing\CouponUses\Pages\CreateCouponUse;
use App\Filament\Resources\Marketing\CouponUses\Pages\EditCouponUse;
use App\Filament\Resources\Marketing\CouponUses\Pages\ListCouponUses;
use App\Filament\Resources\Marketing\CouponUses\Schemas\CouponUseForm;
use App\Filament\Resources\Marketing\CouponUses\Tables\CouponUsesTable;
use App\Models\Marketing\CouponUse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CouponUseResource extends Resource
{
    protected static ?string $model = CouponUse::class;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return CouponUseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponUsesTable::configure($table);
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
            'index' => ListCouponUses::route('/'),
            'create' => CreateCouponUse::route('/create'),
            'edit' => EditCouponUse::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
