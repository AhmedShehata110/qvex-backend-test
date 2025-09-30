<?php

namespace App\Filament\Resources\UsersAndVendors\VendorStaff;

use App\Filament\Resources\UsersAndVendors\VendorStaff\Pages\CreateVendorStaff;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Pages\EditVendorStaff;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Pages\ListVendorStaff;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Pages\ViewVendorStaff;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Schemas\VendorStaffForm;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Schemas\VendorStaffInfolist;
use App\Filament\Resources\UsersAndVendors\VendorStaff\Tables\VendorStaffTable;
use App\Models\Vendor\VendorStaff;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorStaffResource extends Resource
{
    protected static ?string $model = VendorStaff::class;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Vendors';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'user.name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return VendorStaffForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorStaffInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorStaffTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVendorStaff::route('/'),
            'create' => CreateVendorStaff::route('/create'),
            'view' => ViewVendorStaff::route('/{record}'),
            'edit' => EditVendorStaff::route('/{record}/edit'),
        ];
    }
}
