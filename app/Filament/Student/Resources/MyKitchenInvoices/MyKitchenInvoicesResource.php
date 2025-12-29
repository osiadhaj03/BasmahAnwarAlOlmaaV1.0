<?php

namespace App\Filament\Student\Resources\MyKitchenInvoices;

use App\Filament\Student\Resources\MyKitchenInvoices\Pages\ListMyKitchenInvoices;
use App\Filament\Student\Resources\MyKitchenInvoices\Tables\MyKitchenInvoicesTable;
use App\Models\KitchenInvoice;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyKitchenInvoicesResource extends Resource
{
    protected static ?string $model = KitchenInvoice::class;

    protected static ?string $slug = 'my-kitchen-invoices';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'الفواتير';

    protected static ?string $modelLabel = 'فاتورة';

    protected static ?string $pluralModelLabel = 'الفواتير';
    
    protected static ?int $navigationSort = 7;

    public static function table(Table $table): Table
    {
        return MyKitchenInvoicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyKitchenInvoices::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->latest('billing_date');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function canViewAny(): bool
    {
        return true;
    }
}
