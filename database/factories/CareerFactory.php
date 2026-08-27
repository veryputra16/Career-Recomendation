<?php

namespace Database\Factories;

use App\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Career>
 */
class CareerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->jobTitle() . ' ' . fake()->randomNumber(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Software Engineering', 'Data & AI', 'Design', 'Infrastructure']),
            'is_active' => true,
        ];
    }
}
