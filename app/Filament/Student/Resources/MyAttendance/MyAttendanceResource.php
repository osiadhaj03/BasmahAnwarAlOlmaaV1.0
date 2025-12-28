<?php

namespace App\Filament\Student\Resources\MyAttendance;

use App\Filament\Student\Resources\MyAttendance\Pages\ListMyAttendance;
use App\Filament\Student\Resources\MyAttendance\Tables\MyAttendanceTable;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $slug = 'my-attendance';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'سجل حضوري';

    protected static ?string $modelLabel = 'حضور';

    protected static ?string $pluralModelLabel = 'سجل حضوري';
    
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return MyAttendanceTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyAttendance::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // عرض سجلات حضور الطالب الحالي فقط
        return parent::getEloquentQuery()
            ->where('student_id', Auth::id());
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function canViewAny(): bool
    {
        return true;
    }
}
