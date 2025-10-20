<?php

namespace App\Filament\Resources\Lessons;

use App\Filament\Resources\Lessons\Pages\CreateLesson;
use App\Filament\Resources\Lessons\Pages\EditLesson;
use App\Filament\Resources\Lessons\Pages\ListLessons;
use App\Filament\Resources\Lessons\Pages\ViewLesson;
use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Lessons\Schemas\LessonInfolist;
use App\Filament\Resources\Lessons\Tables\LessonsTable;
use App\Models\Lesson;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'الدورات';
    
    protected static ?string $modelLabel = 'دورة';
    
    protected static ?string $pluralModelLabel = 'الدورات';
    protected static UnitEnum|string|null $navigationGroup = 'إدارة الدورات';

    
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // إذا كان المستخدم معلم، عرض دوراته فقط
        if (Auth::check() && Auth::user()->type === 'teacher') {
            $query->where('teacher_id', Auth::id());
        }
        
        // إذا كان المستخدم طالب، عرض الدورات المسجل فيها فقط
        if (Auth::check() && Auth::user()->type === 'student') {
            $query->whereHas('students', function ($studentQuery) {
                $studentQuery->where('student_id', Auth::id());
            });
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        // فقط المدراء يمكنهم إنشاء دروس جديدة
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public static function canEdit(Model $record): bool
    {
        // فقط المدراء يمكنهم تعديل الدروس
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        // فقط المدراء يمكنهم حذف الدروس
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public static function canView(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم رؤية جميع الدروس
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم رؤية دروسهم فقط
        if ($user->type === 'teacher') {
            return $record->teacher_id === $user->id;
        }

        // الطلاب يمكنهم رؤية الدروس المسجلين فيها فقط
        if ($user->type === 'student') {
            return $record->students()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLessons::route('/'),
            'create' => CreateLesson::route('/create'),
            'view' => ViewLesson::route('/{record}'),
            'edit' => EditLesson::route('/{record}/edit'),
        ];
    }
}
