<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'location' => fake()->city() . ', Indonesia',
            'employment_type' => fake()->randomElement(['Full-time', 'Part-time', 'Contract', 'Internship']),
            'type' => fake()->randomElement(['job', 'internship']),
            'source' => 'Internal Platform',
            'source_url' => null,
            'application_url' => null,
            'deadline' => fake()->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the job is an internship.
     */
    public function internship(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'internship',
            'employment_type' => 'Internship',
        ]);
    }

    /**
     * Indicate that the job is a regular full-time job.
     */
    public function job(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'job',
            'employment_type' => 'Full-time',
        ]);
    }
}
