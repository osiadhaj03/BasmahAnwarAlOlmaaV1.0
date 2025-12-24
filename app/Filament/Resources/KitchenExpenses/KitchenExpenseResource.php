<?php

namespace App\Filament\Resources\KitchenExpenses;

use App\Filament\Resources\KitchenExpenses\Pages\CreateKitchenExpense;
use App\Filament\Resources\KitchenExpenses\Pages\EditKitchenExpense;
use App\Filament\Resources\KitchenExpenses\Pages\ListKitchenExpenses;
use App\Filament\Resources\KitchenExpenses\Schemas\KitchenExpenseForm;
use App\Filament\Resources\KitchenExpenses\Tables\KitchenExpensesTable;
use App\Models\KitchenExpense;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KitchenExpenseResource extends Resource
{
    protected static ?string $model = KitchenExpense::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $recordTitleAttribute = 'KitchenExpense';

    protected static UnitEnum|string|null $navigationGroup = 'إدارة الاشتراكات و الدفعات';

    protected static ?string $navigationLabel = 'المصاريف';

    protected static ?string $modelLabel = 'المصاريف';

    protected static ?string $pluralModelLabel = 'المصاريف';

    public static function form(Schema $schema): Schema
    {
        return KitchenExpenseForm::configure($schema);
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
            'create' => CreateKitchenExpense::route('/create'),
            'edit' => EditKitchenExpense::route('/{record}/edit'),
        ];
    }
}
