<?php

use Database\Seeders\CurriculumEntrySeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\PrerequisiteSeeder;
use Database\Seeders\PrerequisiteSubjectSeeder;
use Database\Seeders\ProgramSeeder;
use Database\Seeders\SubjectCategorySeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Support\Facades\DB;

it('seeds prerequisite_subjects from the csv source data', function () {
    $this->seed([
        ProgramSeeder::class,
        SubjectSeeder::class,
        SubjectCategorySeeder::class,
        CurriculumSeeder::class,
        CurriculumEntrySeeder::class,
        PrerequisiteSeeder::class,
        PrerequisiteSubjectSeeder::class,
    ]);

    $expectedAssignments = CvsuSeedData::prerequisiteSubjects();

    expect(DB::table('prerequisite_subjects')->count())->toBe($expectedAssignments->count())
        ->and(DB::table('prerequisite_subjects')->where([
            'prerequisite_id' => 3,
            'curriculum_entry_id' => 23,
        ])->exists())->toBeTrue()
        ->and(DB::table('prerequisite_subjects')->where([
            'prerequisite_id' => 4,
            'curriculum_entry_id' => 26,
        ])->exists())->toBeTrue();
});

it('does not duplicate prerequisite_subject rows when the seeder is rerun', function () {
    $this->seed([
        ProgramSeeder::class,
        SubjectSeeder::class,
        SubjectCategorySeeder::class,
        CurriculumSeeder::class,
        CurriculumEntrySeeder::class,
        PrerequisiteSeeder::class,
        PrerequisiteSubjectSeeder::class,
    ]);

    $expectedAssignments = CvsuSeedData::prerequisiteSubjects()->count();

    $this->seed(PrerequisiteSubjectSeeder::class);

    expect(DB::table('prerequisite_subjects')->count())->toBe($expectedAssignments);
});
