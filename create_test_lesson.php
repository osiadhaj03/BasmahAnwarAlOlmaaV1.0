<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;

echo "=== إنشاء درس تجريبي للاختبار ===\n\n";

// إنشاء درس تجريبي بوقت صباحي
$lesson = new Lesson();
$lesson->title = 'درس تجريبي - اختبار الوقت';
$lesson->description = 'درس تجريبي لاختبار التحقق من الوقت في واجهة الويب';
$lesson->teacher_id = 1;
$lesson->start_date = now()->format('Y-m-d');
$lesson->end_date = now()->addDays(30)->format('Y-m-d');
$lesson->lesson_days = json_encode(['thursday']);
$lesson->start_time = now()->setTime(8, 0, 0)->format('Y-m-d H:i:s'); // 8:00 صباحاً
$lesson->end_time = now()->setTime(9, 0, 0)->format('Y-m-d H:i:s'); // 9:00 صباحاً
$lesson->location_type = 'online';
$lesson->location_details = 'اختبار واجهة الويب';
$lesson->is_recurring = true;
$lesson->status = 'active';
$lesson->max_students = 30;
$lesson->lesson_section_id = null;

try {
    $lesson->save();
    echo "✅ تم إنشاء درس تجريبي بنجاح!\n";
    echo "رقم الدرس: {$lesson->id}\n";
    echo "العنوان: {$lesson->title}\n";
    echo "أيام الدرس: " . json_encode($lesson->lesson_days) . "\n";
    echo "وقت البداية: {$lesson->start_time}\n";
    echo "وقت النهاية: {$lesson->end_time}\n\n";
    
    $now = now();
    echo "الوقت الحالي: " . $now->format('Y-m-d H:i:s') . "\n";
    echo "هل نحن في وقت الدرس؟ " . ($lesson->isCurrentlyInLessonTime() ? 'نعم' : 'لا') . "\n";
    
    if (!$lesson->isCurrentlyInLessonTime()) {
        echo "رسالة الخطأ المتوقعة: " . $lesson->getOutOfTimeErrorMessage() . "\n";
    }
    
    echo "\nيمكنك الآن اختبار النظام من خلال واجهة الويب:\n";
    echo "http://127.0.0.1:8000/attendance/create\n";
    echo "اختر الدرس رقم {$lesson->id} وحاول تسجيل حضور يدوي\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في إنشاء الدرس: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى ===\n";