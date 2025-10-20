<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->time('H:i:s');
        $endTime = date('H:i:s', strtotime($startTime . ' +2 hours'));
        
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'teacher_id' => \App\Models\User::factory(),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'lesson_days' => $this->faker->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], 2),
            'location_type' => $this->faker->randomElement(['online', 'offline']),
            'location_details' => $this->faker->address(),
            'meeting_link' => $this->faker->url(),
            'is_recurring' => $this->faker->boolean(),
            'status' => $this->faker->randomElement(['scheduled', 'active', 'completed', 'cancelled']),
            'max_students' => $this->faker->numberBetween(10, 50),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
