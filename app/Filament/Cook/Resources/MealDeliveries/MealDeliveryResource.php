<?php

namespace App\Filament\Cook\Resources\MealDeliveries;

use App\Filament\Cook\Resources\MealDeliveries\Pages\CreateMealDelivery;
use App\Filament\Cook\Resources\MealDeliveries\Pages\EditMealDelivery;
use App\Filament\Cook\Resources\MealDeliveries\Pages\ListMealDeliveries;
use App\Filament\Cook\Resources\MealDeliveries\Schemas\MealDeliveryForm;
use App\Filament\Cook\Resources\MealDeliveries\Tables\MealDeliveriesTable;
use App\Models\MealDelivery;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MealDeliveryResource extends Resource
{
    protected static ?string $model = MealDelivery::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum|string|null $navigationGroup = 'إدارة الوجبات';

    protected static ?string $navigationLabel = 'تسليم الوجبات';

    protected static ?string $modelLabel = 'تسليم';

    protected static ?string $pluralModelLabel = 'تسليمات الوجبات';

    protected static ?int $navigationSort = 2;

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

    // فلترة حسب مطبخ الطباخ
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->when($user?->kitchen_id, function ($query) use ($user) {
                $query->whereHas('subscription', function ($q) use ($user) {
                    $q->where('kitchen_id', $user->kitchen_id);
                });
            });
    }
}
