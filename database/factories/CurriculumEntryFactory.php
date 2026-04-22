<?php

namespace Database\Factories;

use App\Models\Curriculum;
use App\Models\CurriculumEntry;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CurriculumEntry>
 */
class CurriculumEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $curriculum = Curriculum::query()->inRandomOrder()->first() ?? Curriculum::factory()->create();
        $subject = Subject::query()->inRandomOrder()->first() ?? Subject::factory()->create();
        $subjectCategory = SubjectCategory::query()->inRandomOrder()->first() ?? SubjectCategory::factory()->create();

        return [
            'curriculum_id' => $curriculum->id,
            'subject_id' => $subject->id,
            'subject_category_id' => $subjectCategory->id,
            'semester' => fake()->randomElement(array_keys(CurriculumEntry::SEMESTERS)),
            'year_level' => fake()->numberBetween(1, 6),
        ];
    }
}
