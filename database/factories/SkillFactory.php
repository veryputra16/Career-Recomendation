<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word() . ' ' . fake()->randomNumber(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['technical', 'soft_skill', 'framework', 'database', 'language', 'tool']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
