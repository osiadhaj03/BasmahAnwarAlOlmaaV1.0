<?php

namespace App\Livewire;

use App\Models\AttendanceCode;
use App\Models\Lesson;
use App\Models\Attendance;
use Livewire\Component;
use Livewire\Attributes\On;

class AttendanceCodeDisplay extends Component
{
    public $attendanceCodeId;
    public $attendanceCode;
    public $lesson;
    public $isAutoRefreshEnabled = true;
    public $secondsUntilRefresh = 0;
    public $attendanceStats = [];

    public function mount($attendanceCodeId)
    {
        $this->attendanceCodeId = $attendanceCodeId;
        $this->loadAttendanceCode();
        $this->loadAttendanceStats();
    }

    public function loadAttendanceCode()
    {
        $this->attendanceCode = AttendanceCode::with(['lesson', 'createdBy'])
            ->find($this->attendanceCodeId);
            
        if (!$this->attendanceCode) {
            abort(404, 'كود الحضور غير موجود');
        }

        $this->lesson = $this->attendanceCode->lesson;
        $this->isAutoRefreshEnabled = $this->attendanceCode->auto_refresh;
        $this->secondsUntilRefresh = $this->attendanceCode->getSecondsUntilNextRefresh();
    }

    public function loadAttendanceStats()
    {
        if (!$this->lesson) return;

        // إجمالي الطلاب المسجلين في الدرس
        $totalStudents = $this->lesson->students()->count();
        
        // الطلاب الذين سجلوا الحضور
        $presentStudents = Attendance::where('lesson_id', $this->lesson->id)
            ->where('status', 'present')
            ->count();
            
        // الطلاب الغائبين
        $absentStudents = $totalStudents - $presentStudents;

        $this->attendanceStats = [
            'total' => $totalStudents,
            'present' => $presentStudents,
            'absent' => $absentStudents,
            'percentage' => $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0
        ];
    }

    #[On('refresh-code')]
    public function refreshCode()
    {
        if ($this->attendanceCode && $this->attendanceCode->shouldRefresh()) {
            $this->attendanceCode->refreshCode();
            $this->loadAttendanceCode();
            $this->loadAttendanceStats();
            
            $this->dispatch('code-refreshed', [
                'newCode' => $this->attendanceCode->code,
                'timestamp' => now()->format('H:i:s')
            ]);
        }
    }

    public function toggleAutoRefresh()
    {
        $this->attendanceCode->update([
            'auto_refresh' => !$this->attendanceCode->auto_refresh
        ]);
        
        $this->isAutoRefreshEnabled = $this->attendanceCode->auto_refresh;
        $this->loadAttendanceCode();
    }

    public function stopDisplay()
    {
        $this->attendanceCode->stopDisplay(auth()->id());
        
        session()->flash('message', 'تم إيقاف عرض الكود بنجاح');
        return redirect()->route('filament.admin.resources.attendance-codes.index');
    }

    public function manualRefresh()
    {
        // تحديث يدوي للكود
        $this->attendanceCode->update([
            'code' => AttendanceCode::generateUniqueCode(),
            'last_refreshed_at' => now(),
        ]);
        
        $this->loadAttendanceCode();
        $this->loadAttendanceStats();
        
        session()->flash('message', 'تم تحديث الكود يدوياً');
    }

    public function render()
    {
        // تحديث البيانات في كل render
        $this->loadAttendanceCode();
        $this->loadAttendanceStats();
        
        return view('livewire.attendance-code-display')
            ->layout('components.layouts.app');
    }
}
