<?php

namespace App\Filament\Resources\Administration\Admins;

use App\Enums\User\UserTypeEnum;
use App\Filament\Resources\Administration\Admins\Pages\CreateAdmin;
use App\Filament\Resources\Administration\Admins\Pages\EditAdmin;
use App\Filament\Resources\Administration\Admins\Pages\ListAdmins;
use App\Filament\Resources\Administration\Admins\Pages\ViewAdmin;
use App\Filament\Resources\Administration\Admins\Schemas\AdminForm;
use App\Filament\Resources\Administration\Admins\Tables\AdminsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.admin_management');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.administration');
    }

    protected static ?string $modelLabel = 'Admin User';

    protected static ?string $pluralModelLabel = 'Admin Users';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Only show admin users in this resource
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('user_type', [UserTypeEnum::ADMIN]);
    }

    /**
     * Only accessible by admins
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->user_type === UserTypeEnum::ADMIN ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
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
            'index' => ListAdmins::route('/'),
            'create' => CreateAdmin::route('/create'),
            'view' => ViewAdmin::route('/{record}'),
            'edit' => EditAdmin::route('/{record}/edit'),
        ];
    }
}
