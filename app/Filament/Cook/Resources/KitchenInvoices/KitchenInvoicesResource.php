<?php

namespace App\Filament\Cook\Resources\KitchenInvoices;

use App\Filament\Cook\Resources\KitchenInvoices\Pages\CreateKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Pages\EditKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Pages\ListKitchenInvoices;
use App\Filament\Cook\Resources\KitchenInvoices\Schemas\KitchenInvoicesForm;
use App\Filament\Cook\Resources\KitchenInvoices\Tables\KitchenInvoicesTable;
use App\Models\KitchenInvoices;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KitchenInvoicesResource extends Resource
{
    protected static ?string $model = KitchenInvoices::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'KitchenInvoices';

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
}
