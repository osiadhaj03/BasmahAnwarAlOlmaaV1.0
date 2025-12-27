<?php

namespace App\Filament\Cook\Resources\Meals;

use App\Filament\Cook\Resources\Meals\Pages\CreateMeal;
use App\Filament\Cook\Resources\Meals\Pages\EditMeal;
use App\Filament\Cook\Resources\Meals\Pages\ListMeals;
use App\Filament\Cook\Resources\Meals\Schemas\MealForm;
use App\Filament\Cook\Resources\Meals\Tables\MealsTable;
use App\Models\Meal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MealResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'إدارة الوجبات';

    protected static ?string $navigationLabel = 'الوجبات';

    protected static ?string $modelLabel = 'وجبة';

    protected static ?string $pluralModelLabel = 'الوجبات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MealForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealsTable::configure($table);
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
            'index' => ListMeals::route('/'),
            'create' => CreateMeal::route('/create'),
            'edit' => EditMeal::route('/{record}/edit'),
        ];
    }

    // فلترة الوجبات حسب مطبخ الطباخ
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->when($user?->kitchen_id, function ($query) use ($user) {
                $query->where('kitchen_id', $user->kitchen_id);
            });
    }
}
