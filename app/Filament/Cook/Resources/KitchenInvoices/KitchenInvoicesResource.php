<?php

namespace App\Filament\Cook\Resources\KitchenInvoices;

use App\Filament\Cook\Resources\KitchenInvoices\Pages\CreateKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Pages\EditKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Pages\ListKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Schemas\KitchenInvoicesForm;
use App\Filament\Cook\Resources\KitchenInvoices\Tables\KitchenInvoicesTable;
use App\Models\KitchenInvoice;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KitchenInvoicesResource extends Resource
{
    protected static ?string $model = KitchenInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static UnitEnum|string|null $navigationGroup = 'المالية';

    protected static ?string $navigationLabel = 'الفواتير';

    protected static ?string $modelLabel = 'فاتورة';

    protected static ?string $pluralModelLabel = 'الفواتير';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return KitchenInvoicesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KitchenInvoicesTable::configure($table);
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
            'index' => ListKitchenInvoices::route('/'),
            'create' => CreateKitchenInvoices::route('/create'),
            'edit' => EditKitchenInvoices::route('/{record}/edit'),
        ];
    }

    // فلترة الفواتير حسب مطبخ الطباخ
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
