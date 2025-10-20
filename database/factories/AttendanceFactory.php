<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\User::factory(),
            'lesson_id' => \App\Models\Lesson::factory(),
            'lecture_id' => \App\Models\Lecture::factory(),
            'attendance_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['present', 'absent', 'late', 'excused']),
            'check_in_time' => $this->faker->optional()->time(),
            'check_out_time' => $this->faker->optional()->time(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
