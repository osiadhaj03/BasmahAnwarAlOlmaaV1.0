<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCode;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AttendanceCodeController extends Controller
{
    /**
     * Display a listing of attendance codes.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AttendanceCode::with(['lesson']);

        // Filter by lesson if provided
        if ($request->has('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'valid':
                    $query->valid();
                    break;
            }
        }

        // Filter by auto-refresh
        if ($request->has('auto_refresh')) {
            $query->autoRefresh();
        }

        $codes = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $codes,
            'message' => 'Attendance codes retrieved successfully'
        ]);
    }

    /**
     * Store a newly created attendance code.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'lesson_id' => 'required|exists:lessons,id',
                'expires_at' => 'nullable|date|after:now',
                'usage_limit' => 'nullable|integer|min:1',
                'auto_refresh' => 'boolean',
                'refresh_interval' => 'nullable|integer|min:' . AttendanceCode::MIN_REFRESH_INTERVAL . '|max:' . AttendanceCode::MAX_REFRESH_INTERVAL,
                'code_length' => 'nullable|integer|min:4|max:12',
            ]);

            // Check if lesson exists and user has permission
            $lesson = Lesson::findOrFail($validated['lesson_id']);

            // Generate attendance code
            $attendanceCode = new AttendanceCode();
            $attendanceCode->lesson_id = $validated['lesson_id'];
            $attendanceCode->code = AttendanceCode::generateUniqueCode($validated['code_length'] ?? AttendanceCode::DEFAULT_CODE_LENGTH);
            $attendanceCode->expires_at = $validated['expires_at'] ?? now()->addHours(2);
            $attendanceCode->usage_limit = $validated['usage_limit'] ?? null;
            $attendanceCode->is_active = true;
            $attendanceCode->auto_refresh = $validated['auto_refresh'] ?? false;
            $attendanceCode->refresh_interval = $validated['refresh_interval'] ?? AttendanceCode::DEFAULT_REFRESH_INTERVAL;

            if ($attendanceCode->auto_refresh) {
                $attendanceCode->last_refreshed_at = now();
            }

            $attendanceCode->save();

            return response()->json([
                'success' => true,
                'data' => $attendanceCode->load('lesson'),
                'message' => 'Attendance code created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attendance code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified attendance code.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::with(['lesson', 'attendances.student'])->findOrFail($id);

            // Get attendance statistics
            $stats = [
                'total_attendances' => $attendanceCode->attendances()->count(),
                'present_count' => $attendanceCode->attendances()->where('status', 'present')->count(),
                'absent_count' => $attendanceCode->attendances()->where('status', 'absent')->count(),
                'late_count' => $attendanceCode->attendances()->where('status', 'late')->count(),
                'usage_count' => $attendanceCode->usage_count,
                'usage_limit' => $attendanceCode->usage_limit,
                'is_expired' => $attendanceCode->isExpired(),
                'is_usage_limit_reached' => $attendanceCode->isUsageLimitReached(),
                'can_be_used' => $attendanceCode->canBeUsed(),
                'seconds_until_refresh' => $attendanceCode->auto_refresh ? $attendanceCode->getSecondsUntilNextRefresh() : null,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'attendance_code' => $attendanceCode,
                    'statistics' => $stats
                ],
                'message' => 'Attendance code retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance code not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified attendance code.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::findOrFail($id);

            $validated = $request->validate([
                'expires_at' => 'nullable|date',
                'usage_limit' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
                'auto_refresh' => 'boolean',
                'refresh_interval' => 'nullable|integer|min:' . AttendanceCode::MIN_REFRESH_INTERVAL . '|max:' . AttendanceCode::MAX_REFRESH_INTERVAL,
            ]);

            $attendanceCode->update($validated);

            return response()->json([
                'success' => true,
                'data' => $attendanceCode->load('lesson'),
                'message' => 'Attendance code updated successfully'
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
                'message' => 'Failed to update attendance code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attendance code.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::findOrFail($id);
            $attendanceCode->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance code deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh the attendance code.
     */
    public function refresh(string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::findOrFail($id);

            if (!$attendanceCode->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot refresh inactive attendance code'
                ], 400);
            }

            $attendanceCode->refreshCode();

            return response()->json([
                'success' => true,
                'data' => $attendanceCode->load('lesson'),
                'message' => 'Attendance code refreshed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh attendance code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start displaying the attendance code.
     */
    public function startDisplay(string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::findOrFail($id);
            $attendanceCode->startDisplay();

            return response()->json([
                'success' => true,
                'data' => $attendanceCode->load('lesson'),
                'message' => 'Attendance code display started'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start display',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop displaying the attendance code.
     */
    public function stopDisplay(string $id): JsonResponse
    {
        try {
            $attendanceCode = AttendanceCode::findOrFail($id);
            $attendanceCode->stopDisplay();

            return response()->json([
                'success' => true,
                'data' => $attendanceCode->load('lesson'),
                'message' => 'Attendance code display stopped'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop display',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate attendance code.
     */
    public function validate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:12',
            ]);

            $attendanceCode = AttendanceCode::where('code', $validated['code'])->first();

            if (!$attendanceCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attendance code'
                ], 404);
            }

            $canBeUsed = $attendanceCode->canBeUsed();
            $isCurrentlyActive = $attendanceCode->isCurrentlyActive();

            return response()->json([
                'success' => true,
                'data' => [
                    'attendance_code' => $attendanceCode->load('lesson'),
                    'can_be_used' => $canBeUsed,
                    'is_currently_active' => $isCurrentlyActive,
                    'is_expired' => $attendanceCode->isExpired(),
                    'is_usage_limit_reached' => $attendanceCode->isUsageLimitReached(),
                ],
                'message' => $canBeUsed ? 'Valid attendance code' : 'Attendance code cannot be used'
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
                'message' => 'Failed to validate attendance code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
