<?php

namespace Database\Factories;

use App\Models\CurriculumEntry;
use App\Models\Prerequisite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prerequisite>
 */
class PrerequisiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $curriculumEntry = CurriculumEntry::query()->inRandomOrder()->first() ?? CurriculumEntry::factory()->create();

        return [
            'curriculum_entry_id' => $curriculumEntry->id,
            'label' => strtoupper(fake()->bothify('PRER###')),
        ];
    }
}
