<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

echo "=== اختبار نظام التحقق من وقت الدرس ===\n\n";

// البحث عن درس موجود أو إنشاء درس جديد
$lesson = Lesson::where('title', 'درس تجريبي للاختبار')->first();

if (!$lesson) {
    // إنشاء درس تجريبي
    $lesson = new Lesson();
    $lesson->title = 'درس تجريبي للاختبار';
    $lesson->description = 'درس لاختبار نظام التحقق من الوقت';
    $lesson->teacher_id = 1; // افتراض وجود مدرس برقم 1
    $lesson->start_date = Carbon::today(); // اليوم
    $lesson->end_date = Carbon::today()->addDays(30); // شهر من اليوم
    $lesson->lesson_days = json_encode(['thursday']); // اليوم الحالي
    $lesson->start_time = '14:30:00'; // 2:30 مساءً
    $lesson->end_time = '15:30:00';   // 3:30 مساءً
    $lesson->location_type = 'offline';
    $lesson->location_details = 'قاعة الاختبار';
    $lesson->is_recurring = true;
    $lesson->lesson_section_id = null; // تجاهل القسم مؤقتاً
    
    try {
        $lesson->save();
        echo "تم إنشاء درس تجريبي جديد برقم: " . $lesson->id . "\n";
    } catch (Exception $e) {
        echo "خطأ في إنشاء الدرس: " . $e->getMessage() . "\n";
        // استخدام درس موجود بدلاً من ذلك
        $lesson = Lesson::first();
        if (!$lesson) {
            echo "لا توجد دروس في قاعدة البيانات\n";
            exit;
        }
        echo "سيتم استخدام الدرس الموجود برقم: " . $lesson->id . "\n";
    }
} else {
    echo "تم العثور على درس تجريبي موجود برقم: " . $lesson->id . "\n";
}
echo "أيام الدرس: " . $lesson->lesson_days . "\n";
echo "وقت البداية: " . $lesson->start_time . "\n";
echo "وقت النهاية: " . $lesson->end_time . "\n\n";

// اختبار الدالة الجديدة
$currentTime = Carbon::now();
echo "الوقت الحالي: " . $currentTime->format('Y-m-d H:i:s') . "\n";
echo "اليوم الحالي: " . $currentTime->format('l') . "\n\n";

// اختبار isCurrentlyInLessonTime
$isInTime = $lesson->isCurrentlyInLessonTime();
echo "هل نحن في وقت الدرس؟ " . ($isInTime ? 'نعم' : 'لا') . "\n";

if (!$isInTime) {
    echo "رسالة الخطأ: " . $lesson->getOutOfTimeErrorMessage() . "\n";
}

echo "\n=== اختبار انتهى ===\n";