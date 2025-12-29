<?php

namespace App\Filament\Student\Resources\MyMeals;

use App\Filament\Student\Resources\MyMeals\Pages\ListMyMeals;
use App\Filament\Student\Resources\MyMeals\Tables\MyMealsTable;
use App\Models\Meal;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

class MyMealsResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static ?string $slug = 'my-meals';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = 'الوجبات';

    protected static ?string $modelLabel = 'وجبة';

    protected static ?string $pluralModelLabel = 'الوجبات';
    
    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return MyMealsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyMeals::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest('meal_date');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function canViewAny(): bool
    {
        return true;
    }
}
