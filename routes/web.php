<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AttendanceCodeDisplay;

Route::get('/', function () {
    return view('welcome');
});

// Attendance Code Display Route
Route::get('/attendance-code/{attendanceCodeId}', AttendanceCodeDisplay::class)
    ->name('attendance-code.display')
    ->middleware(['auth']);

// Alternative route for lesson-based code display
Route::get('/lesson/{lessonId}/attendance-code', function ($lessonId) {
    // Find the active attendance code for this lesson
    $attendanceCode = \App\Models\AttendanceCode::where('lesson_id', $lessonId)
        ->where('is_active', true)
        ->first();
    
    if (!$attendanceCode) {
        abort(404, 'لا يوجد كود حضور نشط لهذا الدرس');
    }
    
    return redirect()->route('attendance-code.display', $attendanceCode->id);
})->name('lesson.attendance-code')->middleware(['auth']);

// Test Routes
Route::get('/test-attendance', function () {
    return view('test-attendance');
})->name('test.attendance');

Route::post('/create-test-data', function (\Illuminate\Http\Request $request) {
    try {
        // Create a test user if not exists
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم تجريبي',
                'password' => bcrypt('password'),
                'role' => 'teacher'
            ]
        );

        // Create a test lesson
        $lesson = \App\Models\Lesson::create([
            'title' => $request->lesson_title,
            'description' => 'درس تجريبي لاختبار نظام الحضور',
            'date' => $request->lesson_date,
            'start_time' => $request->start_time,
            'end_time' => date('H:i', strtotime($request->start_time . ' +1 hour')),
            'teacher_id' => $user->id,
            'status' => 'active'
        ]);

        // Create an attendance code
        $attendanceCode = \App\Models\AttendanceCode::create([
            'lesson_id' => $lesson->id,
            'code' => \App\Models\AttendanceCode::generateUniqueCode(),
            'expires_at' => now()->addHours(2),
            'is_active' => true,
            'auto_refresh' => true,
            'refresh_interval' => $request->refresh_interval,
            'display_started_at' => now(),
            'last_refreshed_at' => now(),
            'created_by' => $user->id,
            'max_usage' => null,
            'usage_count' => 0
        ]);

        return redirect()->route('test.attendance')
            ->with('success', 'تم إنشاء الدرس وكود الحضور بنجاح! معرف الكود: ' . $attendanceCode->id);

    } catch (\Exception $e) {
        return redirect()->route('test.attendance')
            ->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
})->name('test.create-data');

// Student attendance input route
Route::get('/student-attendance', \App\Livewire\StudentAttendanceInput::class)->name('student.attendance');

// Timezone test route
Route::get('/test-timezone', function () {
    $now = now();
    $utcTime = now('UTC');
    
    return response()->json([
        'current_timezone' => config('app.timezone'),
        'local_time' => $now->format('Y-m-d H:i:s T'),
        'utc_time' => $utcTime->format('Y-m-d H:i:s T'),
        'timezone_offset' => $now->format('P'),
        'formatted_arabic' => $now->locale('ar')->translatedFormat('l، j F Y - H:i:s'),
    ]);
})->name('test.timezone');
