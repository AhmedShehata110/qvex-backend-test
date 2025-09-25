<?php

namespace App\Filament\Resources\Administration\Roles;

use App\Enums\User\UserTypeEnum;
use App\Filament\Resources\Administration\Roles\Pages\CreateRole;
use App\Filament\Resources\Administration\Roles\Pages\EditRole;
use App\Filament\Resources\Administration\Roles\Pages\ListRoles;
use App\Filament\Resources\Administration\Roles\Schemas\RoleForm;
use App\Filament\Resources\Administration\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Only accessible by super admins
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->user_type === UserTypeEnum::SUPER_ADMIN ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
