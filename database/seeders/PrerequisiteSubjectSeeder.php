<?php

namespace Database\Seeders;

use App\Models\CurriculumEntry;
use App\Models\Prerequisite;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PrerequisiteSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prerequisiteIds = Prerequisite::query()->pluck('id');
        $curriculumEntryIds = CurriculumEntry::query()->pluck('id');
        $timestamp = now();

        $records = CvsuSeedData::prerequisiteSubjects()->map(function (array $assignment) use ($prerequisiteIds, $curriculumEntryIds, $timestamp): array {
            if (! $prerequisiteIds->contains($assignment['prerequisite_legacy_id'])) {
                throw new RuntimeException("Unable to seed prerequisite subject [{$assignment['legacy_id']}] because prerequisite id [{$assignment['prerequisite_legacy_id']}] is missing.");
            }

            if (! $curriculumEntryIds->contains($assignment['curriculum_entry_legacy_id'])) {
                throw new RuntimeException("Unable to seed prerequisite subject [{$assignment['legacy_id']}] because curriculum entry id [{$assignment['curriculum_entry_legacy_id']}] is missing.");
            }

            return [
                'prerequisite_id' => $assignment['prerequisite_legacy_id'],
                'curriculum_entry_id' => $assignment['curriculum_entry_legacy_id'],
                'created_at' => $assignment['created_at'] ?? $timestamp,
                'updated_at' => $assignment['updated_at'] ?? $timestamp,
            ];
        })->all();

        DB::table('prerequisite_subjects')->upsert(
            $records,
            ['prerequisite_id', 'curriculum_entry_id'],
            ['updated_at']
        );
    }
}
