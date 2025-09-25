<?php

namespace App\Filament\Resources\Utilities\FailedJobs;

use App\Filament\Resources\Utilities\FailedJobs\Pages\CreateFailedJob;
use App\Filament\Resources\Utilities\FailedJobs\Pages\EditFailedJob;
use App\Filament\Resources\Utilities\FailedJobs\Pages\ListFailedJobs;
use App\Filament\Resources\Utilities\FailedJobs\Schemas\FailedJobForm;
use App\Filament\Resources\Utilities\FailedJobs\Tables\FailedJobsTable;
use App\Models\System\FailedJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|UnitEnum|null $navigationGroup = 'Utilities';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FailedJobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FailedJobsTable::configure($table);
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
            'index' => ListFailedJobs::route('/'),
            'create' => CreateFailedJob::route('/create'),
            'edit' => EditFailedJob::route('/{record}/edit'),
        ];
    }
}
