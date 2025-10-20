<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lecture>
 */
class LectureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'lesson_id' => \App\Models\Lesson::factory(),
            'lecture_number' => $this->faker->numberBetween(1, 20),
            'lecture_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'duration_minutes' => $this->faker->randomElement([60, 90, 120]),
            'location' => $this->faker->address(),
            'status' => $this->faker->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->paragraph(),
            'recording_url' => $this->faker->optional()->url(),
            'materials' => $this->faker->optional()->randomElements(['slides.pdf', 'notes.docx', 'video.mp4'], 2),
            'is_mandatory' => $this->faker->boolean(80), // 80% chance of being mandatory
        ];
    }
}
