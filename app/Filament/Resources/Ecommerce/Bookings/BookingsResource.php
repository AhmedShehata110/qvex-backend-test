<?php

namespace App\Filament\Resources\Ecommerce\Bookings;

use App\Filament\Resources\Ecommerce\Bookings\Pages\CreateBookings;
use App\Filament\Resources\Ecommerce\Bookings\Pages\EditBookings;
use App\Filament\Resources\Ecommerce\Bookings\Pages\ListBookings;
use App\Filament\Resources\Ecommerce\Bookings\Schemas\BookingsForm;
use App\Filament\Resources\Ecommerce\Bookings\Tables\BookingsTable;
use App\Models\Transaction\Booking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BookingsResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|UnitEnum|null $navigationGroup = 'E-commerce';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return BookingsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
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
            'index' => ListBookings::route('/'),
            'create' => CreateBookings::route('/create'),
            'edit' => EditBookings::route('/{record}/edit'),
        ];
    }
}
