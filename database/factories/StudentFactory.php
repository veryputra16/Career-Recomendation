<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_number' => 'STD' . fake()->unique()->numerify('#####'),
            'full_name' => fake()->name(),
            'university' => 'Universitas Indonesia',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Teknik Informatika',
            'semester' => fake()->numberBetween(1, 8),
            'graduation_year' => fake()->numberBetween(2025, 2028),
            'bio' => fake()->paragraph(),
        ];
    }
}
