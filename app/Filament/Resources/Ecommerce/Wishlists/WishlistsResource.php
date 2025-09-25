<?php

namespace App\Filament\Resources\Ecommerce\Wishlists;

use App\Filament\Resources\Ecommerce\Wishlists\Pages\CreateWishlists;
use App\Filament\Resources\Ecommerce\Wishlists\Pages\EditWishlists;
use App\Filament\Resources\Ecommerce\Wishlists\Pages\ListWishlists;
use App\Filament\Resources\Ecommerce\Wishlists\Schemas\WishlistsForm;
use App\Filament\Resources\Ecommerce\Wishlists\Tables\WishlistsTable;
use App\Models\Customer\UserFavorite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WishlistsResource extends Resource
{
    protected static ?string $model = UserFavorite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'E-commerce';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Wishlists';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return WishlistsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WishlistsTable::configure($table);
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
            'index' => ListWishlists::route('/'),
            'create' => CreateWishlists::route('/create'),
            'edit' => EditWishlists::route('/{record}/edit'),
        ];
    }
}
