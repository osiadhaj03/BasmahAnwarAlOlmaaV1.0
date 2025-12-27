<?php

namespace App\Filament\Resources\LessonSectionStudents;

use App\Filament\Resources\LessonSectionStudents\Pages\CreateLessonSectionStudent;
use App\Filament\Resources\LessonSectionStudents\Pages\EditLessonSectionStudent;
use App\Filament\Resources\LessonSectionStudents\Pages\ListLessonSectionStudents;
use App\Filament\Resources\LessonSectionStudents\Pages\ViewLessonSectionStudent;
use App\Filament\Resources\LessonSectionStudents\Schemas\LessonSectionStudentForm;
use App\Filament\Resources\LessonSectionStudents\Schemas\LessonSectionStudentInfolist;
use App\Filament\Resources\LessonSectionStudents\Tables\LessonSectionStudentsTable;
use App\Models\LessonSectionStudent;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LessonSectionStudentResource extends Resource
{
    protected static ?string $model = LessonSectionStudent::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'تسجيل الطلاب';

    protected static ?string $modelLabel = 'تسجيل طالب';

    protected static ?string $pluralModelLabel = 'تسجيل الطلاب';

    protected static UnitEnum|string|null $navigationGroup = 'إدارة التسجيل';

    protected static ?int $navigationSort = 1;

    // إخفاء من لوحة الطباخ
    public static function shouldRegisterNavigation(): bool
    {
        return filament()->getCurrentPanel()?->getId() !== 'cook';
    }

    public static function form(Schema $schema): Schema
    {
        return LessonSectionStudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonSectionStudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonSectionStudentsTable::configure($table);
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
            'index' => ListLessonSectionStudents::route('/'),
            'create' => CreateLessonSectionStudent::route('/create'),
            'view' => ViewLessonSectionStudent::route('/{record}'),
            'edit' => EditLessonSectionStudent::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->type === 'admin' ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->type === 'admin' ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->type === 'admin' ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->type === 'admin' ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->type === 'admin' ?? false;
    }
}
