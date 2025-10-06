<?php

namespace App\Filament\Resources\Content\Testimonials;

use App\Filament\Resources\Content\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Content\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Content\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Content\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Content\Testimonials\Tables\TestimonialsTable;
use App\Models\Content\Testimonial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('keys.testimonials');
    }

    public static function getNavigationGroup(): string
    {
        return __('keys.content');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
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
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
