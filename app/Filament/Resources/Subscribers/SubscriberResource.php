<?php

namespace App\Filament\Resources\Subscribers;

use App\Filament\Resources\Subscribers\Pages\CreateSubscriber;
use App\Filament\Resources\Subscribers\Pages\EditSubscriber;
use App\Filament\Resources\Subscribers\Pages\ListSubscribers;
use App\Filament\Resources\Subscribers\Pages\ViewSubscriber;
use App\Filament\Resources\Subscribers\Tables\SubscribersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'المشتركين';
    
    protected static ?string $modelLabel = 'مشترك';
    
    protected static ?string $pluralModelLabel = 'المشتركين';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static UnitEnum|string|null $navigationGroup ='إدارة الاشتراكات و الدفعات';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function shouldRegisterNavigation(): bool
    {
        // يظهر في لوحة المدير والطباخ فقط
        $panelId = filament()->getCurrentPanel()?->getId();
        return in_array($panelId, ['admin', 'cook']);
    }

    public static function table(Table $table): Table
    {
        return SubscribersTable::configure($table);
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
            'index' => ListSubscribers::route('/'),
            //'view' => ViewSubscriber::route('/{record}'),
            //'edit' => EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // المشتركين = من لديه اشتراك طعام أو دور زبون
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->whereHas('roles', fn ($q) => $q->where('slug', 'customer'))
                      ->orWhereHas('kitchenSubscriptions');
            });
    }

    public static function canCreate(): bool
    {
        return false; // لا إنشاء من هنا، بل من إدارة الاشتراكات
    }

    public static function canViewAny(): bool
    {
        // السماح للمدير والطباخ برؤية المشتركين
        $panelId = filament()->getCurrentPanel()?->getId();
        return in_array($panelId, ['admin', 'cook']);
    }
}
