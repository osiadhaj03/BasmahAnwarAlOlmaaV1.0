<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'lesson_section_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'lesson_days',
        'location_type',
        'location_details',
        'meeting_link',
        'is_recurring',
        'status',
        'max_students',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'lesson_days' => 'array',
        'is_recurring' => 'boolean',
        'max_students' => 'integer',
    ];

    // العلاقات
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessonSection()
    {
        return $this->belongsTo(LessonSection::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'lesson_student', 'lesson_id', 'student_id')
                    ->withPivot('enrolled_at', 'enrollment_status', 'notes')
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class);
    }

    public function activeAttendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class)->where('is_active', true);
    }

    // Helper methods
    public function getLessonDaysArabicAttribute()
    {
        $daysMap = [
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
        ];

        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return '';
        }

        $arabicDays = array_map(function($day) use ($daysMap) {
            return $daysMap[strtolower($day)] ?? $day;
        }, $this->lesson_days);

        return implode(', ', $arabicDays);
    }

    public function getTitleWithTeacherAttribute()
    {
        $teacherName = $this->teacher ? $this->teacher->name : 'غير محدد';
        return $this->title . ' - ' . $teacherName;
    }

    public function getFullDateTimeAttribute()
    {
        $dateRange = $this->start_date->format('Y-m-d');
        if ($this->end_date && $this->end_date != $this->start_date) {
            $dateRange .= ' إلى ' . $this->end_date->format('Y-m-d');
        }
        return $dateRange . ' ' . $this->start_time . ' - ' . $this->end_time;
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getEnrolledStudentsCountAttribute()
    {
        return $this->students()->wherePivot('enrollment_status', 'enrolled')->count();
    }

    public function getAttendanceRateAttribute()
    {
        $totalStudents = $this->enrolledStudentsCount;
        if ($totalStudents === 0) return 0;
        
        $presentStudents = $this->attendances()->where('status', 'present')->count();
        return round(($presentStudents / $totalStudents) * 100, 2);
    }

    public function canEnrollMoreStudents()
    {
        return $this->max_students === null || $this->enrolledStudentsCount < $this->max_students;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('lesson_date', now()->toDateString());
    }

    /**
     * حساب التاريخ والوقت للدرس التالي بناءً على أيام الدرس المحددة
     */
    public function getNextLessonDateTime()
    {
        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return null;
        }

        $today = now();
        $currentDayName = strtolower($today->format('l')); // sunday, monday, etc.
        
        // تحويل أيام الأسبوع إلى أرقام (0 = Sunday, 6 = Saturday)
        $dayNumbers = [];
        $dayMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];

        foreach ($this->lesson_days as $day) {
            if (isset($dayMap[strtolower($day)])) {
                $dayNumbers[] = $dayMap[strtolower($day)];
            }
        }

        if (empty($dayNumbers)) {
            return null;
        }

        sort($dayNumbers); // ترتيب الأيام

        $currentDayNumber = $today->dayOfWeek;
        $nextLessonDay = null;

        // البحث عن اليوم التالي للدرس
        foreach ($dayNumbers as $dayNumber) {
            if ($dayNumber > $currentDayNumber) {
                $nextLessonDay = $dayNumber;
                break;
            }
        }

        // إذا لم نجد يوم في نفس الأسبوع، نأخذ أول يوم من الأسبوع التالي
        if ($nextLessonDay === null) {
            $nextLessonDay = $dayNumbers[0];
        }

        // حساب التاريخ
        $daysToAdd = $nextLessonDay - $currentDayNumber;
        if ($daysToAdd <= 0) {
            $daysToAdd += 7; // الأسبوع التالي
        }

        $nextLessonDate = $today->copy()->addDays($daysToAdd);

        // دمج التاريخ مع وقت بداية الدرس
        $startTime = Carbon::parse($this->start_time);
        $nextLessonDateTime = $nextLessonDate->copy()
            ->setHour($startTime->hour)
            ->setMinute($startTime->minute)
            ->setSecond(0);

        return $nextLessonDateTime;
    }

    /**
     * التحقق من أن التاريخ والوقت المحدد يقع في أوقات الدرس
     */
    public function isValidLessonDateTime($dateTime)
    {
        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return false;
        }

        $checkDate = Carbon::parse($dateTime);
        $dayName = strtolower($checkDate->format('l'));

        // التحقق من أن اليوم من أيام الدرس
        $lessonDays = array_map('strtolower', $this->lesson_days);
        if (!in_array($dayName, $lessonDays)) {
            return false;
        }

        // التحقق من أن الوقت ضمن فترة الدرس
        $lessonStart = Carbon::parse($this->start_time);
        $lessonEnd = Carbon::parse($this->end_time);
        $checkTime = $checkDate->format('H:i');

        return $checkTime >= $lessonStart->format('H:i') && $checkTime <= $lessonEnd->format('H:i');
    }

    /**
     * تحديد حالة الحضور بناءً على وقت التسجيل
     */
    public function getAttendanceStatus($registrationTime)
    {
        if (!$this->isValidLessonDateTime($registrationTime)) {
            return 'absent'; // خارج أوقات الدرس
        }

        $regTime = Carbon::parse($registrationTime);
        $lessonStart = Carbon::parse($this->start_time);
        
        // إنشاء تاريخ كامل لبداية الدرس في نفس يوم التسجيل
        $lessonStartDateTime = $regTime->copy()
            ->setHour($lessonStart->hour)
            ->setMinute($lessonStart->minute)
            ->setSecond(0);

        // إذا سجل قبل أو في وقت بداية الدرس + 15 دقيقة = حاضر
        $lateThreshold = $lessonStartDateTime->copy()->addMinutes(15);
        
        if ($regTime <= $lateThreshold) {
            return 'present';
        } else {
            return 'late';
        }
    }

    /**
     * الحصول على التاريخ والوقت الافتراضي لتسجيل الحضور
     */
    public function getDefaultAttendanceDateTime()
    {
        $nextLesson = $this->getNextLessonDateTime();
        
        // إذا كان اليوم الحالي من أيام الدرس والوقت الحالي ضمن فترة الدرس
        $now = now();
        if ($this->isValidLessonDateTime($now)) {
            return $now;
        }
        
        // وإلا نعطي موعد الدرس التالي
        return $nextLesson ?: $now;
    }

    /**
     * التحقق من أن الوقت الحالي ضمن وقت الدورة الفعلي
     * يُستخدم لمنع التسجيل اليدوي خارج أوقات الدورة
     */
    public function isCurrentlyInLessonTime()
    {
        $now = now();
        
        // التحقق من أن اليوم الحالي من أيام الدرس
        $currentDayName = strtolower($now->format('l'));
        
        // تحويل lesson_days من JSON إلى array إذا كان string
        $lessonDaysArray = is_string($this->lesson_days) 
            ? json_decode($this->lesson_days, true) 
            : ($this->lesson_days ?? []);
            
        $lessonDays = array_map('strtolower', $lessonDaysArray);
        
        if (!in_array($currentDayName, $lessonDays)) {
            return false;
        }
        
        // التحقق من أن الوقت الحالي ضمن فترة الدرس
        $lessonStart = Carbon::parse($this->start_time);
        $lessonEnd = Carbon::parse($this->end_time);
        $currentTime = $now->format('H:i');
        
        return $currentTime >= $lessonStart->format('H:i') && $currentTime <= $lessonEnd->format('H:i');
    }

    /**
     * الحصول على رسالة خطأ مناسبة عند محاولة التسجيل خارج وقت الدورة
     */
    public function getOutOfTimeErrorMessage()
    {
        $now = now();
        $currentDayName = strtolower($now->format('l'));
        
        // تحويل lesson_days من JSON إلى array إذا كان string
        $lessonDaysArray = is_string($this->lesson_days) 
            ? json_decode($this->lesson_days, true) 
            : ($this->lesson_days ?? []);
            
        $lessonDays = array_map('strtolower', $lessonDaysArray);
        
        $daysMap = [
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
        ];
        
        $arabicDays = array_map(function($day) use ($daysMap) {
            return $daysMap[$day] ?? $day;
        }, $lessonDays);
        
        $daysText = implode(', ', $arabicDays);
        $timeText = Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
        
        if (!in_array($currentDayName, $lessonDays)) {
            return "لا يمكن تسجيل الحضور اليوم. أيام الدورة هي: {$daysText}";
        } else {
            return "لا يمكن تسجيل الحضور في هذا الوقت. وقت الدورة: {$timeText}";
        }
    }

    /**
     * حساب التاريخ والوقت للدرس الحالي إذا كان نشطاً الآن
     */
    public function getCurrentLessonDateTime()
    {
        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return null;
        }

        $now = now();
        $currentDayName = strtolower($now->format('l')); // sunday, monday, etc.
        
        // التحقق من أن اليوم الحالي هو يوم درس
        $lessonDays = array_map('strtolower', $this->lesson_days);
        if (!in_array($currentDayName, $lessonDays)) {
            return null; // ليس يوم درس
        }

        // التحقق من أن الوقت الحالي ضمن وقت الدرس أو قريب منه
        $lessonStart = Carbon::parse($this->start_time);
        $lessonEnd = Carbon::parse($this->end_time);
        $currentTime = Carbon::parse($now->format('H:i:s'));
        
        // هامش دقيقة واحدة قبل الدرس ودقيقة واحدة بعد انتهاء الدرس
        $allowedStart = $lessonStart->copy()->subMinutes(1);
        $allowedEnd = $lessonEnd->copy()->addMinutes(1);
        
        if ($currentTime->between($allowedStart, $allowedEnd)) {
            // الدرس نشط الآن أو قريب من وقته، إرجاع تاريخ ووقت اليوم
            return $now->copy()->setTime($lessonStart->hour, $lessonStart->minute, 0);
        }
        
        return null; // الدرس غير نشط الآن
    }

    /**
     * الحصول على أفضل تاريخ ووقت للدرس (الحالي أولاً، ثم القادم)
     */
    public function getBestLessonDateTime()
    {
        // محاولة الحصول على الدرس الحالي أولاً
        $currentDateTime = $this->getCurrentLessonDateTime();
        if ($currentDateTime) {
            return $currentDateTime;
        }
        
        // إذا لم يكن هناك درس حالي، الحصول على الدرس القادم
        return $this->getNextLessonDateTime();
    }

    /**
     * دالة مساعدة لتوضيح حالة الدرس الحالي (للاختبار والتطوير)
     */
    public function getCurrentLessonStatus()
    {
        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return ['status' => 'no_days', 'message' => 'لا توجد أيام محددة للدرس'];
        }

        $now = now();
        $currentDayName = strtolower($now->format('l'));
        $lessonDays = array_map('strtolower', $this->lesson_days);
        
        if (!in_array($currentDayName, $lessonDays)) {
            return [
                'status' => 'wrong_day', 
                'message' => "اليوم الحالي ({$currentDayName}) ليس من أيام الدرس",
                'lesson_days' => $lessonDays
            ];
        }

        $lessonStart = Carbon::parse($this->start_time);
        $lessonEnd = Carbon::parse($this->end_time);
        $currentTime = Carbon::parse($now->format('H:i:s'));
        
        $allowedStart = $lessonStart->copy()->subMinutes(1);
        $allowedEnd = $lessonEnd->copy()->addMinutes(1);
        
        return [
            'status' => $currentTime->between($allowedStart, $allowedEnd) ? 'active' : 'inactive',
            'current_time' => $currentTime->format('H:i:s'),
            'lesson_start' => $lessonStart->format('H:i:s'),
            'lesson_end' => $lessonEnd->format('H:i:s'),
            'allowed_start' => $allowedStart->format('H:i:s'),
            'allowed_end' => $allowedEnd->format('H:i:s'),
            'is_in_range' => $currentTime->between($allowedStart, $allowedEnd),
            'current_day' => $currentDayName,
            'lesson_days' => $lessonDays
        ];
    }
}
