<?php

namespace App\Filament\Resources\UsersAndVendors\SubscriptionPlans;

use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Pages\CreateSubscriptionPlan;
use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Pages\EditSubscriptionPlan;
use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Pages\ListSubscriptionPlans;
use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Schemas\SubscriptionPlanForm;
use App\Filament\Resources\UsersAndVendors\SubscriptionPlans\Tables\SubscriptionPlansTable;
use App\Models\Vendor\SubscriptionPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Vendors';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return SubscriptionPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionPlansTable::configure($table);
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
            'index' => ListSubscriptionPlans::route('/'),
            'create' => CreateSubscriptionPlan::route('/create'),
            'edit' => EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
