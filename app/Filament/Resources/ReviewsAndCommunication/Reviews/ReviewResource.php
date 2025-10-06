<?php

namespace App\Filament\Resources\ReviewsAndCommunication\Reviews;

use App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages\CreateReview;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages\EditReview;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages\ListReviews;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Pages\ViewReview;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Schemas\ReviewForm;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Schemas\ReviewInfolist;
use App\Filament\Resources\ReviewsAndCommunication\Reviews\Tables\ReviewsTable;
use App\Models\Communication\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|UnitEnum|null $navigationGroup = 'Reviews & Communication';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('keys.reviews');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.reviews_communication');
    }

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReviewInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
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
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'view' => ViewReview::route('/{record}'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
