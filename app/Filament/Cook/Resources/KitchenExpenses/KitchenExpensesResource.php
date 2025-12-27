<?php

namespace App\Filament\Cook\Resources\KitchenExpenses;

use App\Filament\Cook\Resources\KitchenExpenses\Pages\CreateKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Pages\EditKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Pages\ListKitchenExpenses;
use App\Filament\Cook\Resources\KitchenExpenses\Schemas\KitchenExpensesForm;
use App\Filament\Cook\Resources\KitchenExpenses\Tables\KitchenExpensesTable;
use App\Models\KitchenExpense;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KitchenExpensesResource extends Resource
{
    protected static ?string $model = KitchenExpense::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum|string|null $navigationGroup = 'المالية';

    protected static ?string $navigationLabel = 'المصروفات';

    protected static ?string $modelLabel = 'مصروف';

    protected static ?string $pluralModelLabel = 'المصروفات';

    protected static ?int $navigationSort = 3;

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

    // فلترة المصروفات حسب مطبخ الطباخ
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->when($user?->kitchen_id, function ($query) use ($user) {
                $query->where('kitchen_id', $user->kitchen_id);
            });
    }
}
