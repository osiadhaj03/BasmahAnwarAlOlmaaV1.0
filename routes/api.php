<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceCodeController;
use App\Http\Controllers\Api\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Attendance Code API Routes
Route::prefix('attendance-codes')->group(function () {
    Route::get('/', [AttendanceCodeController::class, 'index'])->name('api.attendance-codes.index');
    Route::post('/', [AttendanceCodeController::class, 'store'])->name('api.attendance-codes.store');
    Route::get('/{id}', [AttendanceCodeController::class, 'show'])->name('api.attendance-codes.show');
    Route::put('/{id}', [AttendanceCodeController::class, 'update'])->name('api.attendance-codes.update');
    Route::delete('/{id}', [AttendanceCodeController::class, 'destroy'])->name('api.attendance-codes.destroy');
    
    // Additional attendance code actions
    Route::post('/{id}/refresh', [AttendanceCodeController::class, 'refresh'])->name('api.attendance-codes.refresh');
    Route::post('/{id}/start-display', [AttendanceCodeController::class, 'startDisplay'])->name('api.attendance-codes.start-display');
    Route::post('/{id}/stop-display', [AttendanceCodeController::class, 'stopDisplay'])->name('api.attendance-codes.stop-display');
    Route::post('/validate', [AttendanceCodeController::class, 'validate'])->name('api.attendance-codes.validate');
});

// Attendance API Routes
Route::prefix('attendances')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('api.attendances.index');
    Route::post('/', [AttendanceController::class, 'store'])->name('api.attendances.store');
    Route::get('/{id}', [AttendanceController::class, 'show'])->name('api.attendances.show');
    Route::put('/{id}', [AttendanceController::class, 'update'])->name('api.attendances.update');
    Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('api.attendances.destroy');
    
    // Statistics and bulk operations
    Route::get('/lesson/{lessonId}/stats', [AttendanceController::class, 'lessonStats'])->name('api.attendances.lesson-stats');
    Route::get('/student/{studentId}/stats', [AttendanceController::class, 'studentStats'])->name('api.attendances.student-stats');
    Route::post('/bulk-update', [AttendanceController::class, 'bulkUpdate'])->name('api.attendances.bulk-update');
});

// Public routes (no authentication required)
Route::prefix('public')->group(function () {
    // Student attendance submission (public access)
    Route::post('/submit-attendance', [AttendanceController::class, 'store'])->name('api.public.submit-attendance');
    
    // Code validation (public access)
    Route::post('/validate-code', [AttendanceCodeController::class, 'validate'])->name('api.public.validate-code');
});