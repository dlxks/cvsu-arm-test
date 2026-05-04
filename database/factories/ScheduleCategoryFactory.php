<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ScheduleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ScheduleCategory>
 */
class ScheduleCategoryFactory extends Factory
{
    public const DEFAULT_CATEGORY_NAMES = [
        'LECTURE',
        'BIO SCI',
        'VET',
        'RLE',
        'CCL',
        'PHY SCI',
        'ENGR',
        'CROP SCI',
        'ENGLISH LAB',
        'NSTP',
        'SPEECH',
        'HRM',
        'CEMDS',
        'AN SCI',
        'TRM',
        'NURSING LAB',
        'CSPEAR',
        'STUDTEACH',
        'STUDENT TEACHING',
        'ENGLISH',
        'MWRLE',
        'PSYC LAB',
        'EDFS',
        'RLE 2',
        'MWRLE 2',
        'THESIS',
        'MWRLE 3',
        'RLE 3',
        'CRIMINOLOGY',
        'BSISA',
        'FISHERY',
        'BSESS',
        'STATISTICS',
        'BIOSCI',
        'PSYCH',
        'LECTUREP',
        'LECTUREB',
        'BS BIO',
    ];

    protected $model = ScheduleCategory::class;

    public function definition(): array
    {
        $name = Str::upper((string) fake()->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function named(string $name): static
    {
        return $this->state(fn (): array => [
            'name' => Str::upper(trim($name)),
            'slug' => Str::slug($name),
            'is_active' => true,
        ]);
    }
}