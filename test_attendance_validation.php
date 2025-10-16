<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Requests\AttendanceRequest;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

echo "=== اختبار نظام التحقق من طلب تسجيل الحضور ===\n\n";

// البحث عن درس تجريبي
$lesson = Lesson::where('title', 'LIKE', '%تجريبي%')->first();

if (!$lesson) {
    echo "لم يتم العثور على درس تجريبي\n";
    exit;
}

echo "تم العثور على درس: {$lesson->title} (ID: {$lesson->id})\n";
echo "أيام الدرس: " . json_encode($lesson->lesson_days) . "\n";
echo "وقت البداية: {$lesson->start_time}\n";
echo "وقت النهاية: {$lesson->end_time}\n\n";

// البحث عن طالب
$student = User::where('type', 'student')->first();

if (!$student) {
    echo "لم يتم العثور على طالب\n";
    exit;
}

echo "تم العثور على طالب: {$student->name} (ID: {$student->id})\n\n";

// محاكاة طلب تسجيل حضور
$requestData = [
    'lesson_id' => $lesson->id,
    'student_id' => $student->id,
    'status' => 'present',
    'attendance_date' => now()->format('Y-m-d'),
    'marked_at' => now()->format('Y-m-d H:i:s'),
    'attendance_method' => 'manual',
    'notes' => 'اختبار تسجيل حضور خارج وقت الدرس'
];

echo "بيانات الطلب:\n";
foreach ($requestData as $key => $value) {
    echo "- {$key}: {$value}\n";
}
echo "\n";

// إنشاء validator يدوياً
$rules = [
    'lesson_id' => 'required|exists:lessons,id',
    'student_id' => 'required|exists:users,id',
    'status' => 'required|in:present,absent,late,excused',
    'attendance_date' => 'required|date',
    'marked_at' => 'required|date',
    'attendance_method' => 'required|in:code,manual,auto',
    'notes' => 'nullable|string|max:500',
];

$validator = Validator::make($requestData, $rules);

// إضافة التحقق المخصص
$validator->after(function ($validator) use ($requestData) {
    $lessonId = $requestData['lesson_id'];
    $studentId = $requestData['student_id'];
    $attendanceMethod = $requestData['attendance_method'];
    
    if ($lessonId && $studentId) {
        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            // التحقق من أن التسجيل اليدوي يتم في الوقت الفعلي للدورة
            if ($attendanceMethod === 'manual') {
                if (!$lesson->isCurrentlyInLessonTime()) {
                    $errorMessage = $lesson->getOutOfTimeErrorMessage();
                    $validator->errors()->add('attendance_method', $errorMessage);
                }
            }
        }
    }
});

echo "نتيجة التحقق:\n";
if ($validator->fails()) {
    echo "❌ فشل التحقق - توجد أخطاء:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- {$error}\n";
    }
} else {
    echo "✅ نجح التحقق - لا توجد أخطاء\n";
}

echo "\n=== انتهى الاختبار ===\n";