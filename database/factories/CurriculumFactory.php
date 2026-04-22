<?php

namespace Database\Factories;

use App\Models\Curriculum;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curriculum>
 */
class CurriculumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $program = Program::query()->inRandomOrder()->first() ?? Program::factory()->create();
        $year = fake()->numberBetween(2018, 2026);

        return [
            'program_id' => $program->id,
            'title' => sprintf('%s-%d', fake()->lexify('CURR'), $year),
            'year_implemented' => $year,
        ];
    }
}
