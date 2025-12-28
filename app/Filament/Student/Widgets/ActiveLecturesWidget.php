<?php

namespace App\Filament\Student\Widgets;

use App\Models\Lecture;
use App\Models\Attendance;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActiveLecturesWidget extends Widget
{
    protected string $view = 'filament.student.widgets.active-lectures-widget';
    
    protected ?string $heading = 'المحاضرات النشطة';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 3;

    // تحديث كل 30 ثانية
    protected static ?string $pollingInterval = '30s';

    public function getLectures(): Collection
    {
        $studentId = Auth::id();
        $now = Carbon::now();
        
        return Lecture::query()
            ->with(['lesson.lessonSection', 'attendances'])
            ->whereHas('lesson.lessonSection.enrolledStudents', function (Builder $query) use ($studentId) {
                $query->where('users.id', $studentId)
                      ->where('lesson_section_student.enrollment_status', 'active');
            })
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereDoesntHave('attendances', function (Builder $query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->orderBy('lecture_date', 'asc')
            ->get()
            ->filter(fn (Lecture $lecture) => $this->canRegisterAttendance($lecture))
            ->values();
    }

    public function canRegisterAttendance(Lecture $lecture): bool
    {
        $studentId = Auth::id();
        $now = Carbon::now();
        $lectureStart = Carbon::parse($lecture->lecture_date);
        $lectureEnd = $lectureStart->copy()->addMinutes($lecture->duration_minutes);
        
        // التحقق من أن المحاضرة نشطة (بدأت ولم تنته بعد)
        $isActive = $now->between($lectureStart, $lectureEnd);
        
        // التحقق من عدم تسجيل الحضور مسبقاً
        $notRegistered = !Attendance::where('lecture_id', $lecture->id)
            ->where('student_id', $studentId)
            ->exists();
            
        return $isActive && $notRegistered;
    }

    public function registerAttendance(int $lectureId): void
    {
        $lecture = Lecture::find($lectureId);
        
        if (!$lecture) {
            Notification::make()
                ->title('خطأ')
                ->body('المحاضرة غير موجودة.')
                ->danger()
                ->send();
            return;
        }

        $studentId = Auth::id();
        
        try {
            // التحقق مرة أخرى من إمكانية التسجيل
            if (!$this->canRegisterAttendance($lecture)) {
                Notification::make()
                    ->title('خطأ في تسجيل الحضور')
                    ->body('لا يمكن تسجيل الحضور في هذا الوقت أو تم تسجيل الحضور مسبقاً.')
                    ->danger()
                    ->send();
                return;
            }

            // تسجيل الحضور
            Attendance::create([
                'lecture_id' => $lecture->id,
                'student_id' => $studentId,
                'attendance_date' => now(),
                'status' => 'present',
                'attendance_method' => 'manual',
                'marked_at' => now(),
                'marked_by' => $studentId,
                'notes' => 'تم التسجيل من ويدجت المحاضرات النشطة'
            ]);

            $lectureTitle = $lecture->lesson?->title ?? $lecture->title;
            
            Notification::make()
                ->title('تم تسجيل الحضور بنجاح ✅')
                ->body("تم تسجيل حضورك في محاضرة: {$lectureTitle}")
                ->success()
                ->send();
                
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            Notification::make()
                ->title('تم تسجيل الحضور مسبقاً')
                ->body('لقد تم تسجيل حضورك بالفعل لهذه المحاضرة.')
                ->warning()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ في تسجيل الحضور')
                ->body('حدث خطأ أثناء تسجيل الحضور. يرجى المحاولة مرة أخرى.')
                ->danger()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        return [
            'lectures' => $this->getLectures(),
        ];
    }

    public function hasAttended(int $lectureId): bool
    {
        return Attendance::where('lecture_id', $lectureId)
            ->where('student_id', Auth::id())
            ->exists();
    }
}