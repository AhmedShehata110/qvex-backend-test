<?php

namespace App\Filament\Resources\ReviewsAndCommunication\SupportTickets;

use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages\CreateSupportTicket;
use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Schemas\SupportTicketForm;
use App\Filament\Resources\ReviewsAndCommunication\SupportTickets\Tables\SupportTicketsTable;
use App\Models\Communication\SupportTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reviews & Communication';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
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
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
