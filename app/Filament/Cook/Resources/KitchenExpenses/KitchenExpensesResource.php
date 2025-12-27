<?php

namespace App\Filament\Cook\Resources\KitchenExpenses;

use App\Filament\Cook\Resources\KitchenExpenses\Pages\CreateKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Pages\EditKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Pages\ListKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Schemas\KitchenExpensesForm;
use App\Filament\Cook\Resources\KitchenExpenses\Tables\KitchenExpensesTable;
use App\Models\KitchenExpenses;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KitchenExpensesResource extends Resource
{
    protected static ?string $model = KitchenExpenses::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'KitchenExpenses';

    public static function form(Schema $schema): Schema
    {
        return KitchenExpensesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KitchenExpensesTable::configure($table);
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
            'index' => ListKitchenExpenses::route('/'),
            'create' => CreateKitchenExpenses::route('/create'),
            'edit' => EditKitchenExpenses::route('/{record}/edit'),
        ];
    }
}
