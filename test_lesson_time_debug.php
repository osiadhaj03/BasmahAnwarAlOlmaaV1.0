<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;

echo "=== اختبار تفصيلي لدالة isCurrentlyInLessonTime ===\n\n";

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

$now = now();
echo "الوقت الحالي: " . $now->format('Y-m-d H:i:s') . "\n";
echo "اليوم الحالي: " . $now->format('l') . "\n\n";

// تحويل lesson_days من JSON إلى array إذا كان string
$lessonDaysArray = is_string($lesson->lesson_days) 
    ? json_decode($lesson->lesson_days, true) 
    : ($lesson->lesson_days ?? []);

echo "أيام الدرس (array): " . json_encode($lessonDaysArray) . "\n";

$lessonDays = array_map('strtolower', $lessonDaysArray);
echo "أيام الدرس (lowercase): " . json_encode($lessonDays) . "\n";

$currentDayName = strtolower($now->format('l'));
echo "اليوم الحالي (lowercase): {$currentDayName}\n";

$isDayMatch = in_array($currentDayName, $lessonDays);
echo "هل اليوم يطابق أيام الدرس؟ " . ($isDayMatch ? 'نعم' : 'لا') . "\n\n";

if ($isDayMatch) {
    // تحويل أوقات الدرس إلى Carbon objects
    $lessonStartTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lesson->start_time);
    $lessonEndTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lesson->end_time);
    
    echo "وقت بداية الدرس: " . $lessonStartTime->format('H:i:s') . "\n";
    echo "وقت نهاية الدرس: " . $lessonEndTime->format('H:i:s') . "\n";
    echo "الوقت الحالي: " . $now->format('H:i:s') . "\n\n";
    
    $isTimeInRange = $now->format('H:i:s') >= $lessonStartTime->format('H:i:s') && 
                     $now->format('H:i:s') <= $lessonEndTime->format('H:i:s');
    
    echo "هل الوقت ضمن نطاق الدرس؟ " . ($isTimeInRange ? 'نعم' : 'لا') . "\n";
}

echo "\nنتيجة دالة isCurrentlyInLessonTime: " . ($lesson->isCurrentlyInLessonTime() ? 'نعم' : 'لا') . "\n";

echo "\n=== انتهى الاختبار ===\n";