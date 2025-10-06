<?php

namespace App\Filament\Resources\UsersAndVendors\Vendors;

use App\Filament\Resources\UsersAndVendors\Vendors\Pages\CreateVendor;
use App\Filament\Resources\UsersAndVendors\Vendors\Pages\EditVendor;
use App\Filament\Resources\UsersAndVendors\Vendors\Pages\ListVendors;
use App\Filament\Resources\UsersAndVendors\Vendors\Pages\ViewVendor;
use App\Filament\Resources\UsersAndVendors\Vendors\Schemas\VendorForm;
use App\Filament\Resources\UsersAndVendors\Vendors\Schemas\VendorInfolist;
use App\Filament\Resources\UsersAndVendors\Vendors\Tables\VendorsTable;
use App\Models\Vendor\Vendor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    public static function getNavigationGroup(): ?string
    {
        return __('keys.users_vendors');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('keys.vendors');
    }

    protected static ?string $recordTitleAttribute = 'business_name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return VendorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorsTable::configure($table);
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
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'view' => ViewVendor::route('/{record}'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }
}
