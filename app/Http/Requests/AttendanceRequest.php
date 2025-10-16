<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lesson;
use Carbon\Carbon;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'lesson_id' => 'required|exists:lessons,id',
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'attendance_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $lessonId = $this->input('lesson_id');
                    if ($lessonId && $value) {
                        $lesson = Lesson::find($lessonId);
                        if ($lesson && !$lesson->isValidLessonDateTime($value)) {
                            $fail('يجب أن يكون تاريخ الحضور في أحد أيام الدرس المحددة وضمن أوقات الدرس.');
                        }
                    }
                }
            ],
            'marked_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $lessonId = $this->input('lesson_id');
                    if ($lessonId && $value) {
                        $lesson = Lesson::find($lessonId);
                        if ($lesson && !$lesson->isValidLessonDateTime($value)) {
                            $fail('يمكن تسجيل الحضور فقط في أيام الدرس المحددة وضمن أوقات الدرس.');
                        }
                    }
                }
            ],
            'used_code' => 'nullable|string|max:10',
            'attendance_method' => 'required|in:code,manual,auto',
            'marked_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lesson_id.required' => 'يجب اختيار الدرس.',
            'lesson_id.exists' => 'الدرس المحدد غير موجود.',
            'student_id.required' => 'يجب اختيار الطالب.',
            'student_id.exists' => 'الطالب المحدد غير موجود.',
            'status.required' => 'يجب تحديد حالة الحضور.',
            'status.in' => 'حالة الحضور غير صحيحة.',
            'attendance_date.required' => 'يجب تحديد تاريخ الحضور.',
            'attendance_date.date' => 'تاريخ الحضور غير صحيح.',
            'marked_at.required' => 'يجب تحديد وقت التسجيل.',
            'marked_at.date' => 'وقت التسجيل غير صحيح.',
            'attendance_method.required' => 'يجب تحديد طريقة التسجيل.',
            'attendance_method.in' => 'طريقة التسجيل غير صحيحة.',
            'used_code.max' => 'الكود المستخدم لا يجب أن يتجاوز 10 أحرف.',
            'marked_by.exists' => 'المستخدم المحدد غير موجود.',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 500 حرف.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // التحقق من أن الطالب مسجل في الدرس
            $lessonId = $this->input('lesson_id');
            $studentId = $this->input('student_id');
            
            if ($lessonId && $studentId) {
                $lesson = Lesson::find($lessonId);
                if ($lesson) {
                    $isEnrolled = $lesson->students()
                        ->where('user_id', $studentId)
                        ->wherePivot('enrollment_status', 'enrolled')
                        ->exists();
                    
                    if (!$isEnrolled) {
                        $validator->errors()->add('student_id', 'الطالب غير مسجل في هذا الدرس.');
                    }
                }
            }

            // تحديد حالة الحضور تلقائياً بناءً على وقت التسجيل
            $attendanceDate = $this->input('attendance_date');
            if ($lessonId && $attendanceDate) {
                $lesson = Lesson::find($lessonId);
                if ($lesson && $lesson->isValidLessonDateTime($attendanceDate)) {
                    $suggestedStatus = $lesson->getAttendanceStatus($attendanceDate);
                    // يمكن إضافة منطق لتحديث الحالة تلقائياً هنا إذا لزم الأمر
                }
            }
        });
    }
}