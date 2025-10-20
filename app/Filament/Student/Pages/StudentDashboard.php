<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Dashboard;

class StudentDashboard extends Dashboard
{
    protected static ?string $title = 'لوحة التحكم';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Student\Widgets\ActiveLecturesWidget::class,
            \App\Filament\Student\Widgets\EnrolledLessonsWidget::class,
            \App\Filament\Student\Widgets\AttendanceHistoryWidget::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
}