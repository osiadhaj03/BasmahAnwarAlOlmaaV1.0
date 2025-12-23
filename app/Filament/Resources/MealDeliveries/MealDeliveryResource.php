<?php

namespace App\Filament\Resources\MealDeliveries;

use App\Filament\Resources\MealDeliveries\Pages\CreateMealDelivery;
use App\Filament\Resources\MealDeliveries\Pages\EditMealDelivery;
use App\Filament\Resources\MealDeliveries\Pages\ListMealDeliveries;
use App\Filament\Resources\MealDeliveries\Schemas\MealDeliveryForm;
use App\Filament\Resources\MealDeliveries\Tables\MealDeliveriesTable;
use App\Models\MealDelivery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MealDeliveryResource extends Resource
{
    protected static ?string $model = MealDelivery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MealDelivery';

    public static function form(Schema $schema): Schema
    {
        return MealDeliveryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealDeliveriesTable::configure($table);
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
            'index' => ListMealDeliveries::route('/'),
            'create' => CreateMealDelivery::route('/create'),
            'edit' => EditMealDelivery::route('/{record}/edit'),
        ];
    }
}
