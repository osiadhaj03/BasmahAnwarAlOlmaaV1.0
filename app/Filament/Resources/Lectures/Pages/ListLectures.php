<?php

namespace App\Filament\Resources\Lectures\Pages;

use App\Filament\Resources\Lectures\LectureResource;
use App\Models\Attendance;
use App\Models\Lecture;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListLectures extends ListRecords
{
    protected static string $resource = LectureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAllAbsences')
                ->label('تسجيل الغياب لجميع المحاضرات')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تسجيل الغياب لجميع المحاضرات')
                ->modalDescription('سيتم تسجيل الغياب لجميع الطلاب الذين لم يحضروا في جميع المحاضرات المنتهية. هل أنت متأكد؟')
                ->modalSubmitActionLabel('نعم، سجّل الغياب')
                ->action(function () {
                    $user = Auth::user();
                    
                    // جلب المحاضرات المنتهية مع العلاقات
                    $lecturesQuery = Lecture::where('lecture_date', '<', now())
                        ->with(['lesson.lessonSection.enrolledStudents', 'lesson.students', 'attendances']);
                    
                    // إذا كان المستخدم معلم، فقط محاضرات دوراته
                    if ($user->type === 'teacher') {
                        $lecturesQuery->whereHas('lesson', function ($query) use ($user) {
                            $query->where('teacher_id', $user->id);
                        });
                    }
                    
                    $lectures = $lecturesQuery->get();
                    
                    if ($lectures->isEmpty()) {
                        Notification::make()
                            ->title('لا توجد محاضرات منتهية')
                            ->info()
                            ->send();
                        return;
                    }
                    
                    $totalAbsences = 0;
                    $processedLectures = 0;
                    $totalLectures = $lectures->count();
                    
                    foreach ($lectures as $lecture) {
                        $lesson = $lecture->lesson;
                        
                        if (!$lesson) {
                            continue;
                        }
                        
                        // جلب الطلاب: أولاً من الدورة مباشرة، ثم من الدبلوم
                        $allStudentIds = $lesson->students()->pluck('users.id')->toArray();
                        
                        // إذا لم يكن هناك طلاب مسجلين في الدورة، جلبهم من الدبلوم
                        if (empty($allStudentIds) && $lesson->lessonSection) {
                            $allStudentIds = $lesson->lessonSection->enrolledStudents()->pluck('users.id')->toArray();
                        }
                        
                        if (empty($allStudentIds)) {
                            continue;
                        }
                        
                        // جلب الطلاب الذين لديهم أي سجل حضور (حاضر، متأخر، أو غائب)
                        $recordedStudentIds = $lecture->attendances()
                            ->pluck('student_id')
                            ->toArray();
                        
                        // الطلاب الغائبين = الكل - المسجلين
                        $absentStudentIds = array_diff($allStudentIds, $recordedStudentIds);
                        
                        if (empty($absentStudentIds)) {
                            continue;
                        }
                        
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
                            $totalAbsences++;
                        }
                        $processedLectures++;
                    }
                    
                    if ($totalAbsences === 0) {
                        Notification::make()
                            ->title('لا يوجد طلاب غائبين جدد')
                            ->body("تم فحص {$totalLectures} محاضرة. جميع الطلاب لديهم سجلات حضور.")
                            ->info()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('تم تسجيل الغياب بنجاح')
                            ->body("تم تسجيل {$totalAbsences} غياب في {$processedLectures} محاضرة")
                            ->success()
                            ->send();
                    }
                })
                ->visible(fn () => Auth::user()->type === 'admin' || Auth::user()->type === 'teacher'),
            
            CreateAction::make(),
        ];
    }
}
