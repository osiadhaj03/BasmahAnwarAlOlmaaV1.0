<?php

namespace App\Filament\Cook\Resources\CustomerGroups;

use App\Filament\Cook\Resources\CustomerGroups\Pages\CreateCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Pages\EditCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Pages\ListCustomerGroups;
use App\Filament\Cook\Resources\CustomerGroups\Schemas\CustomerGroupsForm;
use App\Filament\Cook\Resources\CustomerGroups\Tables\CustomerGroupsTable;
use App\Models\CustomerGroup;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerGroupsResource extends Resource
{
    protected static ?string $model = CustomerGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'المشتركين';

    protected static ?string $navigationLabel = 'مجموعات الزبائن';

    protected static ?string $modelLabel = 'مجموعة';

    protected static ?string $pluralModelLabel = 'مجموعات الزبائن';

    protected static ?int $navigationSort = 2;

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

    // فلترة المجموعات حسب مطبخ الطباخ
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->when($user?->kitchen_id, function ($query) use ($user) {
                $query->where('kitchen_id', $user->kitchen_id);
            });
    }
}
