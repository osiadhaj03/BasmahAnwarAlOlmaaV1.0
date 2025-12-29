<?php

namespace App\Filament\Student\Resources\MyKitchenPayments;

use App\Filament\Student\Resources\MyKitchenPayments\Pages\ListMyKitchenPayments;
use App\Filament\Student\Resources\MyKitchenPayments\Tables\MyKitchenPaymentsTable;
use App\Models\KitchenPayment;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyKitchenPaymentsResource extends Resource
{
    protected static ?string $model = KitchenPayment::class;

    protected static ?string $slug = 'my-kitchen-payments';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'الدفعات';

    protected static ?string $modelLabel = 'دفعة';

    protected static ?string $pluralModelLabel = 'الدفعات';
    
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return MyKitchenPaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyKitchenPayments::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $studentId = Auth::id();
        
        return parent::getEloquentQuery()
            ->whereHas('subscription', function ($query) use ($studentId) {
                $query->where('user_id', $studentId);
            });
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function canViewAny(): bool
    {
        return true;
    }
}
