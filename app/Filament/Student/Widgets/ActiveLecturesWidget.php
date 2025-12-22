<?php

namespace App\Filament\Student\Widgets;

use App\Models\Lecture;
use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ActiveLecturesWidget extends BaseWidget
{
    protected static ?string $heading = 'المحاضرات النشطة';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                ///TextColumn::make('title')
                ///    ->label('عنوان المحاضرة')
                ///    
                ///    ->sortable(),
                    
                TextColumn::make('lesson.title')
                    ->label('اسم الدورة')
                    
                    ->sortable(),
                    
                TextColumn::make('lecture_date')
                    ->label('تاريخ ووقت المحاضرة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                    
                //TextColumn::make('duration_minutes')
                //    ->label('المدة')
                //    ->formatStateUsing(fn ($state) => $state . ' دقيقة')
                //    ->sortable(),
                    
                //TextColumn::make('location')
                //    ->label('الموقع'),
                //    
                //TextColumn::make('status_arabic')
                //    ->label('الحالة')
                //    ->badge()
                //    ->color(fn (string $state): string => match ($state) {
                //        'scheduled' => 'warning',
                //        'ongoing' => 'success',
                //        'completed' => 'gray',
                //        'cancelled' => 'danger',
                //        default => 'gray',
                //    }),
            ])
            ->actions([
                Action::make('register_attendance')
                    ->label('تسجيل الحضور')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Lecture $record) {
                        $this->registerAttendance($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد تسجيل الحضور')
                    ->modalDescription('هل أنت متأكد من تسجيل حضورك في هذه المحاضرة؟')
                    ->modalSubmitActionLabel('تسجيل الحضور')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Lecture $record) => $this->canRegisterAttendance($record))
            ])
            ->defaultSort('lecture_date', 'asc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s'); // تحديث كل 30 ثانية
    }

    protected function getTableQuery(): Builder
    {
        $studentId = Auth::id();
        $now = Carbon::now();
        
        return Lecture::query()
            ->with(['lesson.lessonSection', 'attendances'])
            ->whereHas('lesson.lessonSection.enrolledStudents', function (Builder $query) use ($studentId) {
                $query->where('users.id', $studentId)
                      ->where('lesson_section_student.enrollment_status', 'active');
            })
            ->where(function (Builder $query) use ($now) {
                // المحاضرات التي بدأت أو ستبدأ قريباً (خلال ساعة) أو ما زالت نشطة
                $query->where(function($q) use ($now) {
                    $q->where('lecture_date', '<=', $now->copy()->addHour())
                      ->where('lecture_date', '>=', $now->copy()->subMinutes(30));
                })->orWhere(function($q) use ($now) {
                    // أو المحاضرات التي بدأت ولم تنته بعد
                    $q->where('lecture_date', '<=', $now)
                      ->whereRaw('DATE_ADD(lecture_date, INTERVAL duration_minutes MINUTE) > ?', [$now]);
                });
            })
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereDoesntHave('attendances', function (Builder $query) use ($studentId) {
                $query->where('student_id', $studentId);
            });
    }

    protected function canRegisterAttendance(Lecture $lecture): bool
    {
        $studentId = Auth::id();
        $now = Carbon::now();
        $lectureStart = Carbon::parse($lecture->lecture_date);
        $lectureEnd = $lectureStart->copy()->addMinutes($lecture->duration_minutes);
        
        // التحقق من أن المحاضرة نشطة (بدأت ولم تنته بعد)
        $isActive = $now->between($lectureStart->copy()->subMinutes(15), $lectureEnd);
        
        // التحقق من عدم تسجيل الحضور مسبقاً
        $notRegistered = !Attendance::where('lecture_id', $lecture->id)
            ->where('student_id', $studentId)
            ->exists();
            
        return $isActive && $notRegistered;
    }

    protected function registerAttendance(Lecture $lecture): void
    {
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

            Notification::make()
                ->title('تم تسجيل الحضور بنجاح')
                ->body("تم تسجيل حضورك في محاضرة: {$lecture->title}")
                ->success()
                ->send();
                
            // تحديث الجدول
            $this->resetTable();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ في تسجيل الحضور')
                ->body('حدث خطأ أثناء تسجيل الحضور. يرجى المحاولة مرة أخرى.')
                ->danger()
                ->send();
        }
    }
}