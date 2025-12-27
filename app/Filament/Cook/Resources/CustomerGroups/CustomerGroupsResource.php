<?php

namespace App\Filament\Cook\Resources\CustomerGroups;

use App\Filament\Cook\Resources\CustomerGroups\Pages\CreateCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Pages\EditCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Pages\ListCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Schemas\CustomerGroupsForm;
use App\Filament\Cook\Resources\CustomerGroups\Tables\CustomerGroupsTable;
use App\Models\CustomerGroups;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerGroupsResource extends Resource
{
    protected static ?string $model = CustomerGroups::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CustomerGroups';

    public static function form(Schema $schema): Schema
    {
        return CustomerGroupsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerGroupsTable::configure($table);
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
            'index' => ListCustomerGroups::route('/'),
            'create' => CreateCustomerGroups::route('/create'),
            'edit' => EditCustomerGroups::route('/{record}/edit'),
        ];
    }
}
