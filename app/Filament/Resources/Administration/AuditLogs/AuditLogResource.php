<?php

namespace App\Filament\Resources\Administration\AuditLogs;

use App\Enums\User\UserTypeEnum;
use App\Filament\Resources\Administration\AuditLogs\Pages\CreateAuditLog;
use App\Filament\Resources\Administration\AuditLogs\Pages\EditAuditLog;
use App\Filament\Resources\Administration\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\Administration\AuditLogs\Schemas\AuditLogForm;
use App\Filament\Resources\Administration\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $recordTitleAttribute = 'event';

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
        return AuditLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
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
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
