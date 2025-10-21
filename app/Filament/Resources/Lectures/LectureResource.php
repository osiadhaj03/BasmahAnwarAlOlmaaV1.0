<?php

namespace App\Filament\Resources\Lectures;

use App\Filament\Resources\Lectures\Pages\CreateLecture;
use App\Filament\Resources\Lectures\Pages\EditLecture;
use App\Filament\Resources\Lectures\Pages\ListLectures;
use App\Filament\Resources\Lectures\Pages\ViewLecture;
use App\Filament\Resources\Lectures\Schemas\LectureForm;
use App\Filament\Resources\Lectures\Schemas\LectureInfolist;
use App\Filament\Resources\Lectures\Tables\LecturesTable;
use App\Models\Lecture;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LectureResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'المحاضرات';

    protected static ?string $modelLabel = 'محاضرة';

    protected static ?string $pluralModelLabel = 'المحاضرات';

    protected static UnitEnum|string|null $navigationGroup = 'إدارة الدورات';
    
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return LectureForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LectureInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LecturesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // إذا كان المستخدم معلم، عرض محاضرات دوراته فقط
        if (Auth::check() && Auth::user()->type === 'teacher') {
            $query->whereHas('lesson', function ($lessonQuery) {
                $lessonQuery->where('teacher_id', Auth::id());
            });
        }
        
        // إذا كان المستخدم طالب، عرض محاضرات الدورات المسجل فيها فقط
        if (Auth::check() && Auth::user()->type === 'student') {
            $query->whereHas('lesson.students', function ($studentQuery) {
                $studentQuery->where('student_id', Auth::id());
            });
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء والمعلمون يمكنهم إنشاء محاضرات
        return in_array($user->type, ['admin', 'teacher']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم تعديل جميع المحاضرات
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم تعديل محاضرات دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lesson->teacher_id === $user->id;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم حذف جميع المحاضرات
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم حذف محاضرات دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lesson->teacher_id === $user->id;
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم رؤية جميع المحاضرات
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم رؤية محاضرات دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lesson->teacher_id === $user->id;
        }

        // الطلاب يمكنهم رؤية محاضرات الدورات المسجلين فيها فقط
        if ($user->type === 'student') {
            return $record->lesson->students()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLectures::route('/'),
            'create' => CreateLecture::route('/create'),
            'view' => ViewLecture::route('/{record}'),
            'edit' => EditLecture::route('/{record}/edit'),
        ];
    }
}
