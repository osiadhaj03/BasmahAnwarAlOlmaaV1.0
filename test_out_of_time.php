<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

echo "=== اختبار تسجيل الحضور خارج وقت الدرس ===\n\n";

// إنشاء درس تجريبي بوقت مختلف (صباحاً)
$lesson = new Lesson();
$lesson->title = 'درس تجريبي - وقت صباحي';
$lesson->description = 'درس تجريبي لاختبار التحقق من الوقت';
$lesson->teacher_id = 1;
$lesson->start_date = now()->format('Y-m-d');
$lesson->end_date = now()->addDays(30)->format('Y-m-d');
$lesson->lesson_days = json_encode(['thursday']);
$lesson->start_time = now()->setTime(8, 0, 0)->format('Y-m-d H:i:s'); // 8:00 صباحاً
$lesson->end_time = now()->setTime(9, 0, 0)->format('Y-m-d H:i:s'); // 9:00 صباحاً
$lesson->location_type = 'online';
$lesson->location_details = 'اختبار';
$lesson->is_recurring = true;
$lesson->status = 'active';
$lesson->max_students = 30;
$lesson->lesson_section_id = null;

try {
    $lesson->save();
    echo "تم إنشاء درس تجريبي برقم: {$lesson->id}\n";
} catch (Exception $e) {
    echo "خطأ في إنشاء الدرس: " . $e->getMessage() . "\n";
    exit;
}

echo "أيام الدرس: " . json_encode($lesson->lesson_days) . "\n";
echo "وقت البداية: {$lesson->start_time}\n";
echo "وقت النهاية: {$lesson->end_time}\n\n";

$now = now();
echo "الوقت الحالي: " . $now->format('Y-m-d H:i:s') . "\n";
echo "اليوم الحالي: " . $now->format('l') . "\n\n";

echo "هل نحن في وقت الدرس؟ " . ($lesson->isCurrentlyInLessonTime() ? 'نعم' : 'لا') . "\n";

if (!$lesson->isCurrentlyInLessonTime()) {
    echo "رسالة الخطأ: " . $lesson->getOutOfTimeErrorMessage() . "\n";
}

// البحث عن طالب
$student = User::where('type', 'student')->first();

if (!$student) {
    echo "لم يتم العثور على طالب\n";
    exit;
}

echo "\nتم العثور على طالب: {$student->name} (ID: {$student->id})\n\n";

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

// حذف الدرس التجريبي
$lesson->delete();
echo "\nتم حذف الدرس التجريبي\n";

echo "\n=== انتهى الاختبار ===\n";