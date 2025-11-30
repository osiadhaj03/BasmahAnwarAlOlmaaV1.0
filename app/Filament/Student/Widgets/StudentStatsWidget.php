<?php

namespace App\Filament\Student\Widgets;

use App\Models\Attendance;
use App\Models\Lecture;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudentStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;
    
    protected static ?int $sort = 0;

    // جعل الويدجت متجاوب مع الهاتف
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 6; // 6 أعمدة للشاشات الكبيرة، يتكيف تلقائياً للهاتف
    }

    protected function getStats(): array
    {
        $studentId = Auth::id();
        $user = Auth::user();

        // إحصائيات الحضور
        $presentCount = Attendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();

        $lateCount = Attendance::where('student_id', $studentId)
            ->where('status', 'late')
            ->count();

        $absentCount = Attendance::where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();

        $excusedCount = Attendance::where('student_id', $studentId)
            ->where('status', 'excused')
            ->count();

        // عدد الدبلومات (الأقسام المسجل فيها)
        $sectionsCount = $user->enrolledSections()
            ->wherePivot('enrollment_status', 'active')
            ->count();

        // عدد الدورات (الدروس في الأقسام المسجل فيها)
        $sectionIds = $user->enrolledSections()
            ->wherePivot('enrollment_status', 'active')
            ->pluck('lessons_sections.id');

        $lessonsCount = \App\Models\Lesson::whereIn('lesson_section_id', $sectionIds)->count();

        // عدد المحاضرات الكلي
        $lessonIds = \App\Models\Lesson::whereIn('lesson_section_id', $sectionIds)->pluck('id');
        $lecturesCount = Lecture::whereIn('lesson_id', $lessonIds)->count();

        // حساب نسبة الحضور
        $totalAttendance = $presentCount + $lateCount + $absentCount + $excusedCount;
        $attendanceRate = $totalAttendance > 0 
            ? round((($presentCount + $lateCount) / $totalAttendance) * 100) 
            : 0;

        return [
            Stat::make('حاضر', $presentCount)
                ->description('عدد مرات الحضور في الوقت')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),

            Stat::make('متأخر', $lateCount)
                ->description('عدد مرات التأخر')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 2, 1, 4, 2, 3, 1])
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),

            Stat::make('غائب', $absentCount)
                ->description('عدد مرات الغياب')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([1, 2, 0, 1, 0, 2, 1])
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),

            Stat::make('معذور', $excusedCount)
                ->description('عدد مرات العذر')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([0, 1, 0, 1, 1, 0, 1])
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),

            Stat::make('الدبلومات', $sectionsCount)
                ->description('عدد الدبلومات المسجل فيها')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),

            Stat::make('الدورات', $lessonsCount)
                ->description('عدد الدورات في دبلوماتك')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-default',
                ]),
        ];
    }
}
