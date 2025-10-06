<?php

namespace App\Filament\Resources\Locations\Addresses;

use App\Filament\Resources\Locations\Addresses\Pages\CreateAddresses;
use App\Filament\Resources\Locations\Addresses\Pages\EditAddresses;
use App\Filament\Resources\Locations\Addresses\Pages\ListAddresses;
use App\Filament\Resources\Locations\Addresses\Schemas\AddressesForm;
use App\Filament\Resources\Locations\Addresses\Tables\AddressesTable;
use App\Models\Customer\UserAddress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AddressesResource extends Resource
{
    protected static ?string $model = UserAddress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('keys.user_addresses');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.locations');
    }

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return AddressesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddressesTable::configure($table);
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
            'index' => ListAddresses::route('/'),
            'create' => CreateAddresses::route('/create'),
            'edit' => EditAddresses::route('/{record}/edit'),
        ];
    }
}
