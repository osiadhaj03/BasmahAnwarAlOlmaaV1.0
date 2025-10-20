<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $lesson;
    protected $otherLesson;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء المستخدمين
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'type' => 'admin'
        ]);
        
        $this->teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
            'type' => 'teacher'
        ]);
        
        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'type' => 'student'
        ]);

        // إنشاء الدروس
        $this->lesson = Lesson::create([
            'title' => 'Test Lesson',
            'description' => 'Test Description',
            'teacher_id' => $this->teacher->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'lesson_days' => ['monday', 'wednesday'],
            'location_type' => 'online',
            'status' => 'scheduled',
            'max_students' => 30
        ]);
        
        $this->otherLesson = Lesson::create([
            'title' => 'Other Lesson',
            'description' => 'Other Description',
            'teacher_id' => $this->admin->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'lesson_days' => ['tuesday', 'thursday'],
            'location_type' => 'offline',
            'status' => 'scheduled',
            'max_students' => 25
        ]);
    }

    /** @test */
    public function teacher_can_view_only_assigned_lessons()
    {
        $this->actingAs($this->teacher);

        // يجب أن يتمكن المعلم من رؤية درسه
        $this->assertTrue($this->teacher->can('view', $this->lesson));

        // يجب ألا يتمكن المعلم من رؤية درس آخر
        $this->assertFalse($this->teacher->can('view', $this->otherLesson));
    }

    /** @test */
    public function teacher_cannot_create_lessons()
    {
        $this->actingAs($this->teacher);

        // يجب ألا يتمكن المعلم من إنشاء دروس
        $this->assertFalse($this->teacher->can('create', Lesson::class));
    }

    /** @test */
    public function teacher_cannot_update_lessons()
    {
        $this->actingAs($this->teacher);

        // يجب ألا يتمكن المعلم من تحديث الدروس
        $this->assertFalse($this->teacher->can('update', $this->lesson));
    }

    /** @test */
    public function teacher_cannot_delete_lessons()
    {
        $this->actingAs($this->teacher);

        // يجب ألا يتمكن المعلم من حذف الدروس
        $this->assertFalse($this->teacher->can('delete', $this->lesson));
    }

    /** @test */
    public function admin_has_full_lesson_permissions()
    {
        $this->actingAs($this->admin);

        // يجب أن يتمكن المدير من جميع العمليات
        $this->assertTrue($this->admin->can('viewAny', Lesson::class));
        $this->assertTrue($this->admin->can('view', $this->lesson));
        $this->assertTrue($this->admin->can('create', Lesson::class));
        $this->assertTrue($this->admin->can('update', $this->lesson));
        $this->assertTrue($this->admin->can('delete', $this->lesson));
    }

    /** @test */
    public function teacher_can_manage_lectures_for_assigned_lessons()
    {
        $this->actingAs($this->teacher);

        $lecture = Lecture::create([
            'title' => 'Test Lecture',
            'description' => 'Test Description',
            'lesson_id' => $this->lesson->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(1),
            'duration_minutes' => 90,
            'location' => 'Online',
            'status' => 'scheduled',
            'is_mandatory' => true
        ]);
        
        $otherLecture = Lecture::create([
            'title' => 'Other Lecture',
            'description' => 'Other Description',
            'lesson_id' => $this->otherLesson->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(2),
            'duration_minutes' => 60,
            'location' => 'Classroom',
            'status' => 'scheduled',
            'is_mandatory' => true
        ]);

        // يجب أن يتمكن المعلم من إدارة محاضرات درسه
        $this->assertTrue($this->teacher->can('view', $lecture));
        $this->assertTrue($this->teacher->can('create', Lecture::class));
        $this->assertTrue($this->teacher->can('update', $lecture));
        $this->assertTrue($this->teacher->can('delete', $lecture));

        // يجب ألا يتمكن من إدارة محاضرات درس آخر
        $this->assertFalse($this->teacher->can('view', $otherLecture));
        $this->assertFalse($this->teacher->can('update', $otherLecture));
        $this->assertFalse($this->teacher->can('delete', $otherLecture));
    }

    /** @test */
    public function teacher_can_manage_attendance_for_assigned_lessons()
    {
        $this->actingAs($this->teacher);

        // إنشاء محاضرات أولاً
        $lecture = Lecture::create([
            'title' => 'Test Lecture',
            'description' => 'Test Description',
            'lesson_id' => $this->lesson->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(1),
            'duration_minutes' => 90,
            'location' => 'Online',
            'status' => 'scheduled',
            'is_mandatory' => true
        ]);
        
        $otherLecture = Lecture::create([
            'title' => 'Other Lecture',
            'description' => 'Other Description',
            'lesson_id' => $this->otherLesson->id,
            'lecture_number' => 1,
            'lecture_date' => now()->addDays(2),
            'duration_minutes' => 60,
            'location' => 'Classroom',
            'status' => 'scheduled',
            'is_mandatory' => true
        ]);

        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'lecture_id' => $lecture->id,
            'attendance_date' => now(),
            'status' => 'present',
            'check_in_time' => '09:00:00'
        ]);
        
        $otherAttendance = Attendance::create([
            'student_id' => $this->student->id,
            'lecture_id' => $otherLecture->id,
            'attendance_date' => now(),
            'status' => 'present',
            'check_in_time' => '14:00:00'
        ]);

        // يجب أن يتمكن المعلم من إدارة حضور درسه
        $this->assertTrue($this->teacher->can('view', $attendance));
        $this->assertTrue($this->teacher->can('create', Attendance::class));
        $this->assertTrue($this->teacher->can('update', $attendance));
        $this->assertTrue($this->teacher->can('delete', $attendance));

        // يجب ألا يتمكن من إدارة حضور درس آخر
        $this->assertFalse($this->teacher->can('view', $otherAttendance));
        $this->assertFalse($this->teacher->can('update', $otherAttendance));
        $this->assertFalse($this->teacher->can('delete', $otherAttendance));
    }

    /** @test */
    public function teacher_can_view_students_in_assigned_lessons()
    {
        $this->actingAs($this->teacher);

        // إضافة طالب للدرس
        $this->lesson->students()->attach($this->student->id);

        // يجب أن يتمكن المعلم من رؤية الطالب
        $this->assertTrue($this->teacher->can('view', $this->student));

        // يجب أن يتمكن من رؤية ملفه الشخصي
        $this->assertTrue($this->teacher->can('view', $this->teacher));
    }

    /** @test */
    public function teacher_cannot_manage_users()
    {
        $this->actingAs($this->teacher);

        // يجب ألا يتمكن المعلم من إنشاء أو تحديث أو حذف المستخدمين
        $this->assertFalse($this->teacher->can('create', User::class));
        $this->assertFalse($this->teacher->can('update', $this->student));
        $this->assertFalse($this->teacher->can('delete', $this->student));
    }
}