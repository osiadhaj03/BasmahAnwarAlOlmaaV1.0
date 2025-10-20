<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Resources\Teachers\Schemas\TeacherInfolist;
use App\Filament\Resources\Teachers\Tables\TeachersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TeacherResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'المعلمين';

    protected static ?string $modelLabel = 'معلم';

    protected static ?string $pluralModelLabel = 'المعلمين';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    
    protected static UnitEnum|string|null $navigationGroup = 'إدارة المعلمين والطلاب';



    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function shouldRegisterNavigation(): bool
    {
        // إخفاء مورد المعلمين عن الطلاب
        return Auth::check() && Auth::user()->type !== 'student';
    }

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeacherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'teacher');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }
}
