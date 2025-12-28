<?php

namespace App\Filament\Student\Resources\MyLectures;

use App\Filament\Student\Resources\MyLectures\Pages\ListMyLectures;
use App\Filament\Student\Resources\MyLectures\Tables\MyLecturesTable;
use App\Models\Lecture;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyLecturesResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static ?string $slug = 'my-lectures';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'محاضراتي';

    protected static ?string $modelLabel = 'محاضرة';

    protected static ?string $pluralModelLabel = 'محاضراتي';
    
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return MyLecturesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyLectures::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $studentId = Auth::id();
        
        // عرض محاضرات الدورات التي الطالب مسجل في أقسامها
        return parent::getEloquentQuery()
            ->whereHas('lesson.lessonSection.enrolledStudents', function ($query) use ($studentId) {
                $query->where('users.id', $studentId)
                      ->where('lesson_section_student.enrollment_status', 'active');
            });
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}
