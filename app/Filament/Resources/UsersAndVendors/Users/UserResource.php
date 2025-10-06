<?php

namespace App\Filament\Resources\UsersAndVendors\Users;

use App\Enums\User\UserTypeEnum;
use App\Filament\Resources\UsersAndVendors\Users\Pages\CreateUser;
use App\Filament\Resources\UsersAndVendors\Users\Pages\EditUser;
use App\Filament\Resources\UsersAndVendors\Users\Pages\ListUsers;
use App\Filament\Resources\UsersAndVendors\Users\Pages\ViewUser;
use App\Filament\Resources\UsersAndVendors\Users\Schemas\UserForm;
use App\Filament\Resources\UsersAndVendors\Users\Schemas\UserInfolist;
use App\Filament\Resources\UsersAndVendors\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationGroup(): ?string
    {
        return __('keys.users_vendors');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.users');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
 /**
     * Only show  users in this resource
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('user_type', [UserTypeEnum::USER]);
    }
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
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
