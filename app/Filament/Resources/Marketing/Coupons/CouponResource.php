<?php

namespace App\Filament\Resources\Marketing\Coupons;

use App\Filament\Resources\Marketing\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Marketing\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Marketing\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Marketing\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Marketing\Coupons\Tables\CouponsTable;
use App\Models\Marketing\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('keys.coupons');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.marketing');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CouponForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
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
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
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
