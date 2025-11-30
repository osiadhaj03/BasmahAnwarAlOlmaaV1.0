<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدمين تجريبيين
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'phone' => '0501234567',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
            'nationality' => 'سعودي',
            'academic_level' => 'bachelor'
        ]);

        $teacher1 = User::create([
            'name' => 'أحمد محمد',
            'email' => 'teacher1@demo.com',
            'password' => Hash::make('password'),
            'type' => 'teacher',
            'phone' => '0501234568',
            'birth_date' => '1985-05-15',
            'gender' => 'male',
            'nationality' => 'سعودي',
            'academic_level' => 'university'
        ]);

        $teacher2 = User::create([
            'name' => 'فاطمة علي',
            'email' => 'teacher2@demo.com',
            'password' => Hash::make('password'),
            'type' => 'teacher',
            'phone' => '0501234569',
            'birth_date' => '1987-08-20',
            'gender' => 'female',
            'nationality' => 'سعودي',
            'academic_level' => 'university'
        ]);

        $student1 = User::create([
            'name' => 'سارة أحمد',
            'email' => 'student1@demo.com',
            'password' => Hash::make('password'),
            'type' => 'student',
            'phone' => '0501234570',
            'birth_date' => '2000-03-10',
            'gender' => 'female',
            'nationality' => 'سعودي',
            'academic_level' => 'high'
        ]);

        $student2 = User::create([
            'name' => 'محمد سالم',
            'email' => 'student2@demo.com',
            'password' => Hash::make('password'),
            'type' => 'student',
            'phone' => '0501234571',
            'birth_date' => '1999-12-05',
            'gender' => 'male',
            'nationality' => 'سعودي',
            'academic_level' => 'high'
        ]);

        $student3 = User::create([
            'name' => 'نورا خالد',
            'email' => 'student3@demo.com',
            'password' => Hash::make('password'),
            'type' => 'student',
            'phone' => '0501234572',
            'birth_date' => '2001-07-18',
            'gender' => 'female',
            'nationality' => 'سعودي',
            'academic_level' => 'middle'
        ]);

        // إنشاء دروس تجريبية
        $lesson1 = Lesson::create([
            'title' => 'أساسيات البرمجة',
            'description' => 'تعلم أساسيات البرمجة باستخدام Python',
            'teacher_id' => $teacher1->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(37),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'lesson_days' => json_encode(['sunday', 'tuesday', 'thursday']),
            'location_type' => 'offline',
            'location_details' => 'قاعة 101',
            'meeting_link' => null,
            'is_recurring' => true,
            'status' => 'scheduled',
            'max_students' => 20,
            'notes' => 'يرجى إحضار جهاز كمبيوتر محمول'
        ]);

        $lesson2 = Lesson::create([
            'title' => 'تطوير المواقع',
            'description' => 'تعلم تطوير المواقع باستخدام HTML, CSS, JavaScript',
            'teacher_id' => $teacher1->id,
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(44),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'lesson_days' => json_encode(['monday', 'wednesday']),
            'location_type' => 'online',
            'location_details' => null,
            'meeting_link' => 'https://zoom.us/j/987654321',
            'is_recurring' => true,
            'status' => 'scheduled',
            'max_students' => 15,
            'notes' => 'سيتم توفير جميع المواد اللازمة'
        ]);

        $lesson3 = Lesson::create([
            'title' => 'الرياضيات المتقدمة',
            'description' => 'دروس في الجبر والهندسة التحليلية',
            'teacher_id' => $teacher2->id,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(33),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'lesson_days' => json_encode(['saturday', 'monday', 'wednesday']),
            'location_type' => 'offline',
            'location_details' => 'قاعة 201',
            'meeting_link' => null,
            'is_recurring' => true,
            'status' => 'active',
            'max_students' => 25,
            'notes' => 'متطلب أساسي: معرفة أساسيات الرياضيات'
        ]);

        // ربط الطلاب بالدروس
        $lesson1->students()->attach([
            $student1->id => ['enrolled_at' => now(), 'enrollment_status' => 'active'],
            $student2->id => ['enrolled_at' => now(), 'enrollment_status' => 'active'],
            $student3->id => ['enrolled_at' => now(), 'enrollment_status' => 'active']
        ]);

        $lesson2->students()->attach([
            $student1->id => ['enrolled_at' => now(), 'enrollment_status' => 'active'],
            $student2->id => ['enrolled_at' => now(), 'enrollment_status' => 'active']
        ]);

        $lesson3->students()->attach([
            $student2->id => ['enrolled_at' => now(), 'enrollment_status' => 'active'],
            $student3->id => ['enrolled_at' => now(), 'enrollment_status' => 'active']
        ]);

        // إنشاء محاضرات تجريبية
        $lecture1 = Lecture::create([
            'title' => 'مقدمة في البرمجة',
            'description' => 'نظرة عامة على البرمجة ولغة Python',
            'lesson_id' => $lesson1->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(7),
            'duration_minutes' => 120,
            'location' => 'قاعة 101',
            'status' => 'scheduled',
            'is_mandatory' => true,
            'notes' => 'المحاضرة الأولى - مهمة جداً'
        ]);

        $lecture2 = Lecture::create([
            'title' => 'المتغيرات وأنواع البيانات',
            'description' => 'تعلم كيفية استخدام المتغيرات في Python',
            'lesson_id' => $lesson1->id,
            'lecture_number' => 2,
            'lecture_date' => now()->addDays(9),
            'duration_minutes' => 120,
            'location' => 'قاعة 101',
            'status' => 'scheduled',
            'is_mandatory' => true,
            'notes' => 'تطبيق عملي على المتغيرات'
        ]);

        $lecture3 = Lecture::create([
            'title' => 'مقدمة في HTML',
            'description' => 'أساسيات لغة HTML لبناء صفحات الويب',
            'lesson_id' => $lesson2->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(14),
            'duration_minutes' => 120,
            'location' => 'قاعة 102',
            'status' => 'scheduled',
            'is_mandatory' => true,
            'notes' => 'إحضار محرر نصوص'
        ]);

        // إنشاء سجلات حضور تجريبية
        Attendance::create([
            'student_id' => $student1->id,
            'lecture_id' => $lecture1->id,
            'attendance_date' => now()->addDays(7),
            'status' => 'present',
            'marked_at' => now(),
            'attendance_method' => 'code',
            'used_code' => 'DEMO123',
            'notes' => 'حضر في الوقت المحدد'
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'lecture_id' => $lecture1->id,
            'attendance_date' => now()->addDays(7),
            'status' => 'late',
            'marked_at' => now(),
            'attendance_method' => 'code',
            'used_code' => 'DEMO123',
            'notes' => 'تأخر 15 دقيقة'
        ]);

        Attendance::create([
            'student_id' => $student3->id,
            'lecture_id' => $lecture1->id,
            'attendance_date' => now()->addDays(7),
            'status' => 'absent',
            'marked_at' => now(),
            'attendance_method' => 'auto',
            'notes' => 'غياب بعذر'
        ]);

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('بيانات تسجيل الدخول:');
        $this->command->info('المدير: admin@demo.com / password');
        $this->command->info('المعلم 1: teacher1@demo.com / password');
        $this->command->info('المعلم 2: teacher2@demo.com / password');
        $this->command->info('الطالب 1: student1@demo.com / password');
        $this->command->info('الطالب 2: student2@demo.com / password');
        $this->command->info('الطالب 3: student3@demo.com / password');
    }
}