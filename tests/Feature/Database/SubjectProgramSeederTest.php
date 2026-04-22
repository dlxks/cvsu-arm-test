<?php

use Database\Seeders\ProgramSeeder;
use Database\Seeders\SubjectProgramSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Support\Facades\DB;

it('seeds subject_program from the csv source data', function () {
    $this->seed([
        ProgramSeeder::class,
        SubjectSeeder::class,
        SubjectProgramSeeder::class,
    ]);

    $expectedAssignments = CvsuSeedData::subjectPrograms();

    expect(DB::table('subject_program')->count())->toBe($expectedAssignments->count())
        ->and(DB::table('subject_program')->where([
            'subject_id' => 21,
            'program_id' => 400,
        ])->exists())->toBeTrue()
        ->and(DB::table('subject_program')->where([
            'subject_id' => 22,
            'program_id' => 427,
        ])->exists())->toBeTrue();
});

it('does not duplicate subject_program rows when the seeder is rerun', function () {
    $this->seed([
        ProgramSeeder::class,
        SubjectSeeder::class,
        SubjectProgramSeeder::class,
    ]);

    $expectedAssignments = CvsuSeedData::subjectPrograms()->count();

    $this->seed(SubjectProgramSeeder::class);

    expect(DB::table('subject_program')->count())->toBe($expectedAssignments);
});
