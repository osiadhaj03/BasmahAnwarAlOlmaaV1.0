<?php

namespace App\Filament\Cook\Resources\KitchenPayments;

use App\Filament\Cook\Resources\KitchenPayments\Pages\CreateKitchenPayment;
use App\Filament\Cook\Resources\KitchenPayments\Pages\EditKitchenPayment;
use App\Filament\Cook\Resources\KitchenPayments\Pages\ListKitchenPayments;
use App\Filament\Cook\Resources\KitchenPayments\Schemas\KitchenPaymentForm;
use App\Filament\Cook\Resources\KitchenPayments\Tables\KitchenPaymentsTable;
use App\Models\KitchenPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KitchenPaymentResource extends Resource
{
    protected static ?string $model = KitchenPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'KitchenPayment';

    public static function form(Schema $schema): Schema
    {
        return KitchenPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KitchenPaymentsTable::configure($table);
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
            'index' => ListKitchenPayments::route('/'),
            'create' => CreateKitchenPayment::route('/create'),
            'edit' => EditKitchenPayment::route('/{record}/edit'),
        ];
    }
}
