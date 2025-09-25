<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Messages;

use App\Filament\Resources\ReviewsAndCommunication\Messages\Pages\CreateMessage;
use App\Filament\Resources\ReviewsAndCommunication\Messages\Pages\EditMessage;
use App\Filament\Resources\ReviewsAndCommunication\Messages\Pages\ListMessages;
use App\Filament\Resources\ReviewsAndCommunication\Messages\Schemas\MessageForm;
use App\Filament\Resources\ReviewsAndCommunication\Messages\Tables\MessagesTable;
use App\Models\Communication\Message;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    protected static string|UnitEnum|null $navigationGroup = 'Reviews & Communication';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $recordTitleAttribute = 'message';

    public static function form(Schema $schema): Schema
    {
        return MessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessagesTable::configure($table);
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
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
            'edit' => EditMessage::route('/{record}/edit'),
        ];
    }
}
