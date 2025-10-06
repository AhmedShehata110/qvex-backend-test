<?php

namespace App\Filament\Resources\UsersAndVendors\VendorSubscriptions;

use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages\CreateVendorSubscription;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages\EditVendorSubscription;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages\ListVendorSubscriptions;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Pages\ViewVendorSubscription;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Schemas\VendorSubscriptionForm;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Schemas\VendorSubscriptionInfolist;
use App\Filament\Resources\UsersAndVendors\VendorSubscriptions\Tables\VendorSubscriptionsTable;
use App\Models\Vendor\VendorSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class VendorSubscriptionResource extends Resource
{
    protected static ?string $model = VendorSubscription::class;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Vendors';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('keys.vendor_subscriptions');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.users_vendors');
    }

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return VendorSubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorSubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorSubscriptionsTable::configure($table);
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
            'index' => ListVendorSubscriptions::route('/'),
            'create' => CreateVendorSubscription::route('/create'),
            'view' => ViewVendorSubscription::route('/{record}'),
            'edit' => EditVendorSubscription::route('/{record}/edit'),
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
