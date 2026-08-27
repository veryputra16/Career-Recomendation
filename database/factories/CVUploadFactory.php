<?php

namespace Database\Factories;

use App\Models\CVUpload;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CVUpload>
 */
class CVUploadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = 'cv_' . fake()->uuid() . '.pdf';

        return [
            'student_id' => Student::factory(),
            'original_filename' => 'My_Resume.pdf',
            'stored_filename' => $filename,
            'file_path' => 'private/cv/' . $filename,
            'file_size' => fake()->numberBetween(100000, 2000000),
            'mime_type' => 'application/pdf',
            'status' => 'uploaded',
            'extracted_text' => null,
            'uploaded_at' => now(),
            'processed_at' => null,
        ];
    }

    /**
     * Indicate that the CV has been processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
            'extracted_text' => fake()->paragraphs(3, true),
            'processed_at' => now(),
        ]);
    }
}
