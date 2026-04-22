<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubjectProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjectIds = Subject::query()->pluck('id');
        $programIds = Program::query()->pluck('id');
        $timestamp = now();

        $records = CvsuSeedData::subjectPrograms()->map(function (array $assignment) use ($subjectIds, $programIds, $timestamp): array {
            if (! $subjectIds->contains($assignment['subject_legacy_id'])) {
                throw new RuntimeException("Unable to seed subject program assignment [{$assignment['legacy_id']}] because subject id [{$assignment['subject_legacy_id']}] is missing.");
            }

            if (! $programIds->contains($assignment['program_legacy_id'])) {
                throw new RuntimeException("Unable to seed subject program assignment [{$assignment['legacy_id']}] because program id [{$assignment['program_legacy_id']}] is missing.");
            }

            return [
                'subject_id' => $assignment['subject_legacy_id'],
                'program_id' => $assignment['program_legacy_id'],
                'created_at' => $assignment['created_at'] ?? $timestamp,
                'updated_at' => $assignment['updated_at'] ?? $timestamp,
            ];
        })->all();

        DB::table('subject_program')->upsert(
            $records,
            ['subject_id', 'program_id'],
            ['updated_at']
        );
    }
}
