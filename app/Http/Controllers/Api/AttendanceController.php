<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\Student;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with(['student', 'lesson', 'attendanceCode']);

        // Filter by lesson if provided
        if ($request->has('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        // Filter by student if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by attendance code if provided
        if ($request->has('attendance_code_id')) {
            $query->where('attendance_code_id', $request->attendance_code_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        
        if ($request->to_date) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }
        
        $attendances = $query->orderBy('attendance_date', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attendances,
            'message' => 'Attendances retrieved successfully'
        ]);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_name' => 'required|string|max:255',
                'student_id' => 'required|string|max:50',
                'attendance_code' => 'required|string|max:12',
            ]);

            // Find the attendance code
            $attendanceCode = AttendanceCode::where('code', $validated['attendance_code'])->first();

            if (!$attendanceCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الحضور غير صحيح'
                ], 404);
            }

            // Check if the code can be used
            if (!$attendanceCode->canBeUsed()) {
                $message = 'كود الحضور غير متاح حالياً';
                
                if ($attendanceCode->isExpired()) {
                    $message = 'كود الحضور منتهي الصلاحية';
                } elseif ($attendanceCode->isUsageLimitReached()) {
                    $message = 'تم الوصول للحد الأقصى لاستخدام الكود';
                } elseif (!$attendanceCode->is_active) {
                    $message = 'كود الحضور غير نشط';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            // Find or create student
            $student = Student::firstOrCreate(
                ['student_id' => $validated['student_id']],
                ['name' => $validated['student_name']]
            );

            // Check if student already attended this lesson
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('lesson_id', $attendanceCode->lesson_id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'لقد قمت بتسجيل الحضور لهذا الدرس مسبقاً',
                    'data' => [
                        'existing_attendance' => $existingAttendance->load(['lesson', 'attendanceCode'])
                    ]
                ], 409);
            }

            // Determine attendance status based on timing
            $status = 'present';
            $lesson = $attendanceCode->lesson;
            
            if ($lesson && $lesson->date && $lesson->start_time) {
                $lessonStart = \Carbon\Carbon::parse($lesson->date . ' ' . $lesson->start_time);
                $now = now();
                
                // If more than 15 minutes late, mark as late
                if ($now->diffInMinutes($lessonStart) > 15 && $now->gt($lessonStart)) {
                    $status = 'late';
                }
            }

            // Create attendance record
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'lesson_id' => $attendanceCode->lesson_id,
                'attendance_code_id' => $attendanceCode->id,
                'status' => $status,
                'attendance_date' => now(),
            ]);

            // Increment usage count
            $attendanceCode->incrementUsage();

            return response()->json([
                'success' => true,
                'data' => [
                    'attendance' => $attendance->load(['student', 'lesson', 'attendanceCode']),
                    'status' => $status,
                    'message' => $status === 'present' ? 'تم تسجيل حضورك بنجاح' : 'تم تسجيل حضورك متأخراً'
                ],
                'message' => 'تم تسجيل الحضور بنجاح'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تسجيل الحضور',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified attendance.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $attendance = Attendance::with(['student', 'lesson', 'attendanceCode'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Attendance retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified attendance.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $attendance = Attendance::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:present,absent,late',
                'attendance_date' => 'nullable|date',
            ]);

            $attendance->update($validated);

            return response()->json([
                'success' => true,
                'data' => $attendance->load(['student', 'lesson', 'attendanceCode']),
                'message' => 'Attendance updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attendance.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $attendance = Attendance::findOrFail($id);
            
            // Decrement usage count from attendance code
            if ($attendance->attendanceCode) {
                $attendance->attendanceCode->decrement('usage_count');
            }
            
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance statistics for a lesson.
     */
    public function lessonStats(string $lessonId): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($lessonId);
            
            $totalAttendances = Attendance::where('lesson_id', $lessonId)->count();
            $presentCount = Attendance::where('lesson_id', $lessonId)->where('status', 'present')->count();
            $absentCount = Attendance::where('lesson_id', $lessonId)->where('status', 'absent')->count();
            $lateCount = Attendance::where('lesson_id', $lessonId)->where('status', 'late')->count();
            
            $attendancePercentage = $totalAttendances > 0 ? round(($presentCount + $lateCount) / $totalAttendances * 100, 2) : 0;

            $stats = [
                'lesson' => $lesson,
                'total_attendances' => $totalAttendances,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'attendance_percentage' => $attendancePercentage,
                'recent_attendances' => Attendance::where('lesson_id', $lessonId)
                    ->with(['student', 'attendanceCode'])
                    ->orderBy('attendance_date', 'desc')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Lesson statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get lesson statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance statistics for a student.
     */
    public function studentStats(string $studentId): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);
            
            $totalAttendances = Attendance::where('student_id', $studentId)->count();
            $presentCount = Attendance::where('student_id', $studentId)->where('status', 'present')->count();
            $absentCount = Attendance::where('student_id', $studentId)->where('status', 'absent')->count();
            $lateCount = Attendance::where('student_id', $studentId)->where('status', 'late')->count();
            
            $attendancePercentage = $totalAttendances > 0 ? round(($presentCount + $lateCount) / $totalAttendances * 100, 2) : 0;

            $stats = [
                'student' => $student,
                'total_attendances' => $totalAttendances,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'attendance_percentage' => $attendancePercentage,
                'recent_attendances' => Attendance::where('student_id', $studentId)
                    ->with(['lesson', 'attendanceCode'])
                    ->orderBy('attendance_date', 'desc')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Student statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get student statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update attendance status.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'attendance_ids' => 'required|array',
                'attendance_ids.*' => 'exists:attendances,id',
                'status' => 'required|in:present,absent,late',
            ]);

            $updated = Attendance::whereIn('id', $validated['attendance_ids'])
                ->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'data' => [
                    'updated_count' => $updated
                ],
                'message' => "تم تحديث {$updated} سجل حضور بنجاح"
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update attendances',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
