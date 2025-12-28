<?php

namespace App\Filament\Student\Resources\MyCourses;

use App\Filament\Student\Resources\MyCourses\Pages\ListMyCourses;
use App\Filament\Student\Resources\MyCourses\Tables\MyCoursesTable;
use App\Models\Lesson;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyCoursesResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $slug = 'my-courses';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'دوراتي';

    protected static ?string $modelLabel = 'دورة';

    protected static ?string $pluralModelLabel = 'دوراتي';
    
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return MyCoursesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyCourses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $studentId = Auth::id();

        // جلب الأقسام المسجل بها الطالب ونشطة
        $sectionIds = Auth::user()?->enrolledSections()
            ->wherePivot('enrollment_status', 'active')
            ->pluck('lessons_sections.id');

        // عرض الدورات التابعة لهذه الأقسام
        return parent::getEloquentQuery()
            ->whereIn('lesson_section_id', $sectionIds ?? []);
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}
