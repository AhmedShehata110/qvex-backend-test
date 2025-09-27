<?php

namespace App\Filament\Resources\Administration\Settings;

use App\Enums\User\UserTypeEnum;
use App\Filament\Resources\Administration\Settings\Pages\CreateSetting;
use App\Filament\Resources\Administration\Settings\Pages\EditSetting;
use App\Filament\Resources\Administration\Settings\Pages\ListSettings;
use App\Filament\Resources\Administration\Settings\Schemas\SettingForm;
use App\Filament\Resources\Administration\Settings\Tables\SettingsTable;
use App\Models\System\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $recordTitleAttribute = 'key';

    /**
     * Only accessible by admins
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user?->user_type === UserTypeEnum::ADMIN ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
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
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
