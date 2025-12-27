<?php

namespace App\Filament\Cook\Resources\KitchenSubscriptions;

use App\Filament\Cook\Resources\KitchenSubscriptions\Pages\CreateKitchenSubscription;
use App\Filament\Cook\Resources\KitchenSubscriptions\Pages\EditKitchenSubscription;
use App\Filament\Cook\Resources\KitchenSubscriptions\Pages\ListKitchenSubscriptions;
use App\Filament\Cook\Resources\KitchenSubscriptions\Schemas\KitchenSubscriptionForm;
use App\Filament\Cook\Resources\KitchenSubscriptions\Tables\KitchenSubscriptionsTable;
use App\Models\KitchenSubscription;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KitchenSubscriptionResource extends Resource
{
    protected static ?string $model = KitchenSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum|string|null $navigationGroup = 'المشتركين';

    protected static ?string $navigationLabel = 'الاشتراكات';

    protected static ?string $modelLabel = 'اشتراك';

    protected static ?string $pluralModelLabel = 'الاشتراكات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return KitchenSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KitchenSubscriptionsTable::configure($table);
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
            'index' => ListKitchenSubscriptions::route('/'),
            'create' => CreateKitchenSubscription::route('/create'),
            'edit' => EditKitchenSubscription::route('/{record}/edit'),
        ];
    }

    // فلترة المشتركين حسب مطبخ الطباخ
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->when($user?->kitchen_id, function ($query) use ($user) {
                $query->where('kitchen_id', $user->kitchen_id);
            });
    }
}
