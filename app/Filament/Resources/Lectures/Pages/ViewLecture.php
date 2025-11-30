<?php

namespace App\Filament\Resources\Lectures\Pages;

use App\Filament\Resources\Lectures\LectureResource;
use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewLecture extends ViewRecord
{
    protected static string $resource = LectureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAbsences')
                ->label('تسجيل الغياب')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تسجيل الغياب')
                ->modalDescription('سيتم تسجيل جميع الطلاب الذين لم يحضروا هذه المحاضرة كغائبين. هل أنت متأكد؟')
                ->modalSubmitActionLabel('نعم، سجّل الغياب')
                ->action(function () {
                    $lecture = $this->record;
                    $lesson = $lecture->lesson;
                    
                    // جلب جميع طلاب الدورة
                    $allStudentIds = $lesson->students()->pluck('users.id')->toArray();
                    
                    // جلب الطلاب الذين سجلوا حضورهم
                    $attendedStudentIds = $lecture->attendances()->pluck('student_id')->toArray();
                    
                    // الطلاب الغائبين = الكل - الحاضرين
                    $absentStudentIds = array_diff($allStudentIds, $attendedStudentIds);
                    
                    if (empty($absentStudentIds)) {
                        Notification::make()
                            ->title('لا يوجد طلاب غائبين')
                            ->body('جميع طلاب الدورة قد سجلوا حضورهم أو تم تسجيل غيابهم مسبقاً')
                            ->info()
                            ->send();
                        return;
                    }
                    
                    $count = 0;
                    foreach ($absentStudentIds as $studentId) {
                        Attendance::create([
                            'lecture_id' => $lecture->id,
                            'student_id' => $studentId,
                            'status' => 'absent',
                            'attendance_date' => $lecture->lecture_date ?? now(),
                            'attendance_method' => 'manual',
                            'marked_by' => Auth::id(),
                            'marked_at' => now(),
                            'notes' => 'تم تسجيل الغياب تلقائياً',
                        ]);
                        $count++;
                    }
                    
                    Notification::make()
                        ->title('تم تسجيل الغياب بنجاح')
                        ->body("تم تسجيل {$count} طالب كغائبين")
                        ->success()
                        ->send();
                })
                ->visible(fn () => Auth::user()->type === 'admin' || Auth::user()->type === 'teacher'),
            
            EditAction::make(),
        ];
    }
}
