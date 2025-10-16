<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AttendanceCode;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class StudentAttendanceInput extends Component
{
    #[Validate('required|string|min:4|max:10')]
    public $attendanceCode = '';
    
    #[Validate('required|string|min:2')]
    public $studentName = '';
    
    #[Validate('required|string|min:3')]
    public $studentId = '';
    
    public $message = '';
    public $messageType = 'info'; // success, error, info, warning
    public $isSubmitting = false;
    public $recentAttendances = [];
    public $currentLesson = null;

    public function mount()
    {
        // إذا كان المستخدم مسجل دخول، املأ البيانات تلقائياً
        if (Auth::check()) {
            $user = Auth::user();
            $this->studentName = $user->name;
            $this->studentId = $user->student_id ?? '';
        }
        
        $this->loadRecentAttendances();
    }

    public function submitAttendance()
    {
        $this->validate();
        
        $this->isSubmitting = true;
        $this->message = '';
        
        try {
            // البحث عن الكود
            $code = AttendanceCode::where('code', $this->attendanceCode)
                ->where('is_active', true)
                ->first();
            
            if (!$code) {
                $this->setMessage('الكود غير صحيح أو منتهي الصلاحية', 'error');
                return;
            }
            
            // التحقق من صلاحية الكود
            if (!$code->canBeUsed()) {
                $this->setMessage('الكود غير صالح للاستخدام حالياً', 'error');
                return;
            }
            
            // التحقق من أن الكود نشط حالياً
            if (!$code->isCurrentlyActive()) {
                $this->setMessage('الكود غير نشط حالياً', 'error');
                return;
            }
            
            // البحث عن الطالب أو إنشاؤه
            $student = User::where('student_id', $this->studentId)->first();
            
            if (!$student) {
                // إنشاء طالب جديد إذا لم يكن موجوداً
                $student = User::create([
                    'name' => $this->studentName,
                    'student_id' => $this->studentId,
                    'email' => $this->studentId . '@student.local',
                    'password' => bcrypt('password'),
                    'type' => 'student'
                ]);
            }
            
            // التحقق من عدم تسجيل الحضور مسبقاً
            $existingAttendance = Attendance::where('lesson_id', $code->lesson_id)
                ->where('student_id', $student->id)
                ->first();
            
            if ($existingAttendance) {
                $this->setMessage('تم تسجيل حضورك مسبقاً لهذا الدرس', 'warning');
                return;
            }
            
            // تسجيل الحضور
            $attendance = Attendance::create([
                'lesson_id' => $code->lesson_id,
                'student_id' => $student->id,
                'attendance_code_id' => $code->id,
                'status' => 'present',
                'attendance_date' => now(),
                'notes' => 'تم التسجيل عبر الكود: ' . $this->attendanceCode
            ]);
            
            // تحديث عداد استخدام الكود
            $code->incrementUsage();
            
            // تحديث معلومات الدرس الحالي
            $this->currentLesson = $code->lesson;
            
            $this->setMessage('تم تسجيل حضورك بنجاح! مرحباً ' . $this->studentName, 'success');
            
            // إعادة تعيين النموذج
            $this->attendanceCode = '';
            
            // تحديث قائمة الحضور الأخيرة
            $this->loadRecentAttendances();
            
        } catch (\Exception $e) {
            $this->setMessage('حدث خطأ أثناء تسجيل الحضور: ' . $e->getMessage(), 'error');
        } finally {
            $this->isSubmitting = false;
        }
    }
    
    public function clearMessage()
    {
        $this->message = '';
        $this->messageType = 'info';
    }
    
    public function resetForm()
    {
        $this->attendanceCode = '';
        $this->message = '';
        $this->messageType = 'info';
    }
    
    private function setMessage($message, $type = 'info')
    {
        $this->message = $message;
        $this->messageType = $type;
    }
    
    private function loadRecentAttendances()
    {
        if (Auth::check()) {
            $this->recentAttendances = Attendance::with(['lesson'])
                ->where('student_id', Auth::id())
                ->orderBy('attendance_date', 'desc')
                ->take(5)
                ->get();
        }
    }
    
    public function checkCodeStatus()
    {
        if (empty($this->attendanceCode)) {
            return;
        }
        
        $code = AttendanceCode::where('code', $this->attendanceCode)->first();
        
        if (!$code) {
            $this->setMessage('الكود غير موجود', 'error');
            return;
        }
        
        if (!$code->is_active) {
            $this->setMessage('الكود غير نشط', 'warning');
            return;
        }
        
        if ($code->isExpired()) {
            $this->setMessage('الكود منتهي الصلاحية', 'warning');
            return;
        }
        
        if (!$code->isCurrentlyActive()) {
            $this->setMessage('الكود غير نشط حالياً', 'warning');
            return;
        }
        
        $this->setMessage('الكود صالح ونشط', 'success');
        $this->currentLesson = $code->lesson;
    }

    public function render()
    {
        return view('livewire.student-attendance-input')
            ->layout('components.layouts.app');
    }
}
