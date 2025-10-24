<?php

namespace App\Filament\Student\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\LessonSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EnrolledLessonsWidget extends BaseWidget
{
    protected static ?string $heading = 'أسماء الدبلومات المسجل بها';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        $studentId = Auth::id();

        $query = LessonSection::query()
            ->whereHas('enrolledStudents', function (Builder $q) use ($studentId) {
                $q->where('users.id', $studentId)
                  ->where('lesson_section_student.enrollment_status', 'active');
            })
            ->withCount('lessons')
            ->with(['enrolledStudents' => function ($q) use ($studentId) {
                $q->where('users.id', $studentId);
            }]);

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->label('اسم القسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-rectangle-stack'),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->placeholder('لا يوجد وصف'),

                TextColumn::make('lessons_count')
                    ->label('عدد الدورات')
                    ->sortable()
                    ->icon('heroicon-o-academic-cap'),

                TextColumn::make('enrolled_at')
                    ->label('تاريخ التسجيل')
                    ->state(fn ($record) => optional($record->enrolledStudents->first()?->pivot)->enrolled_at)
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),


            ])
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }
}