<?php

namespace App\Observers;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceObserver
{
    /**
     * Handle the Attendance "creating" event.
     */
    public function creating(Attendance $attendance): void
    {
        // التحقق من أن التسجيل اليدوي يتم في الوقت الفعلي للدورة
        if ($attendance->attendance_method === 'manual' && $attendance->lesson) {
            if (!$attendance->lesson->isCurrentlyInLessonTime()) {
                $errorMessage = $attendance->lesson->getOutOfTimeErrorMessage();
                throw new \Exception($errorMessage);
            }
        }

        // تعيين وقت التسجيل إذا لم يتم تحديده
        if (!$attendance->marked_at) {
            $attendance->marked_at = now();
        }

        // تعيين المستخدم الذي سجل الحضور
        if (!$attendance->marked_by && auth()->check()) {
            $attendance->marked_by = auth()->id();
        }

        // تحديد طريقة التسجيل إذا لم تكن محددة
        if (!$attendance->attendance_method) {
            $attendance->attendance_method = $attendance->used_code ? 'code' : 'manual';
        }

        // تحديد حالة الحضور تلقائياً إذا لم تكن محددة
        if (!$attendance->status && $attendance->lesson && $attendance->attendance_date) {
            $suggestedStatus = $attendance->lesson->getAttendanceStatus($attendance->attendance_date);
            if ($suggestedStatus) {
                $attendance->status = $suggestedStatus;
            }
        }

        // تعيين تاريخ الحضور الافتراضي
        if (!$attendance->attendance_date && $attendance->lesson) {
            $defaultDateTime = $attendance->lesson->getDefaultAttendanceDateTime();
            if ($defaultDateTime) {
                $attendance->attendance_date = $defaultDateTime;
            }
        }
    }

    /**
     * Handle the Attendance "updating" event.
     */
    public function updating(Attendance $attendance): void
    {
        // إعادة تحديد حالة الحضور إذا تم تغيير تاريخ الحضور
        if ($attendance->isDirty('attendance_date') && $attendance->lesson) {
            $suggestedStatus = $attendance->lesson->getAttendanceStatus($attendance->attendance_date);
            if ($suggestedStatus && $attendance->status !== $suggestedStatus) {
                // يمكن تطبيق التحديث التلقائي أو تركه للمستخدم
                // $attendance->status = $suggestedStatus;
            }
        }

        // تحديث طريقة التسجيل بناءً على وجود الكود
        if ($attendance->isDirty('used_code')) {
            if ($attendance->used_code && $attendance->attendance_method !== 'code') {
                $attendance->attendance_method = 'code';
            } elseif (!$attendance->used_code && $attendance->attendance_method === 'code') {
                $attendance->attendance_method = 'manual';
            }
        }
    }

    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        // يمكن إضافة منطق إضافي بعد إنشاء سجل الحضور
        // مثل إرسال إشعارات أو تحديث الإحصائيات
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function updated(Attendance $attendance): void
    {
        // يمكن إضافة منطق إضافي بعد تحديث سجل الحضور
    }

    /**
     * Handle the Attendance "deleted" event.
     */
    public function deleted(Attendance $attendance): void
    {
        // يمكن إضافة منطق إضافي بعد حذف سجل الحضور
    }

    /**
     * Handle the Attendance "restored" event.
     */
    public function restored(Attendance $attendance): void
    {
        //
    }

    /**
     * Handle the Attendance "force deleted" event.
     */
    public function forceDeleted(Attendance $attendance): void
    {
        //
    }
}