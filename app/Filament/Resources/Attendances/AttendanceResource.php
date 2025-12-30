<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\CreateAttendance;
use App\Filament\Resources\Attendances\Pages\EditAttendance;
use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'الحضور';

    protected static ?string $modelLabel = 'حضور';

    protected static ?string $pluralModelLabel = 'الحضور';
    
    protected static UnitEnum|string|null $navigationGroup = 'إدارة الدورات';



    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        // إخفاء عن الطلاب ولوحة الطباخ
        if (filament()->getCurrentPanel()?->getId() === 'cook') {
            return false;
        }
        return Auth::check() && Auth::user()->type !== 'student';
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // إذا كان المستخدم معلم، عرض حضور طلاب دوراته فقط
        if (Auth::check() && Auth::user()->type === 'teacher') {
            $query->whereHas('lecture.lesson', function ($lessonQuery) {
                $lessonQuery->where('teacher_id', Auth::id());
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

        // المدراء والمعلمون يمكنهم إنشاء سجلات حضور
        return in_array($user->type, ['admin', 'teacher']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم تعديل جميع سجلات الحضور
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم تعديل حضور طلاب دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lecture->lesson->teacher_id === $user->id;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم حذف جميع سجلات الحضور
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم حذف حضور طلاب دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lecture->lesson->teacher_id === $user->id;
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم رؤية جميع سجلات الحضور
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم رؤية حضور طلاب دوراتهم فقط
        if ($user->type === 'teacher') {
            return $record->lecture->lesson->teacher_id === $user->id;
        }

        return false;
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
