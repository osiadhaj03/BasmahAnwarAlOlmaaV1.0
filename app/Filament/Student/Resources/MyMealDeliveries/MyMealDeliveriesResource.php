<?php

namespace App\Filament\Student\Resources\MyMealDeliveries;

use App\Filament\Student\Resources\MyMealDeliveries\Pages\ListMyMealDeliveries;
use App\Filament\Student\Resources\MyMealDeliveries\Tables\MyMealDeliveriesTable;
use App\Models\MealDelivery;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyMealDeliveriesResource extends Resource
{
    protected static ?string $model = MealDelivery::class;

    protected static ?string $slug = 'my-meal-deliveries';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'سجل استلام الوجبات';

    protected static ?string $modelLabel = 'سجل استلام';

    protected static ?string $pluralModelLabel = 'سجل استلام الوجبات';
    
    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return MyMealDeliveriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyMealDeliveries::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function canViewAny(): bool
    {
        return true;
    }
}
