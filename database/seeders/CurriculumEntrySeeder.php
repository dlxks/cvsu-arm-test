<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\CurriculumEntry;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;
use RuntimeException;

class CurriculumEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curriculumIds = Curriculum::query()->pluck('id');
        $subjectIds = Subject::query()->pluck('id');
        $subjectCategoryIds = SubjectCategory::query()->pluck('id');
        $timestamp = now();

        CvsuSeedData::curriculumEntries()->each(function (array $entry) use ($curriculumIds, $subjectIds, $subjectCategoryIds, $timestamp): void {
            if (! $curriculumIds->contains($entry['curriculum_legacy_id'])) {
                throw new RuntimeException("Unable to seed curriculum entry [{$entry['legacy_id']}] because curriculum id [{$entry['curriculum_legacy_id']}] is missing.");
            }

            if (! $subjectIds->contains($entry['subject_legacy_id'])) {
                throw new RuntimeException("Unable to seed curriculum entry [{$entry['legacy_id']}] because subject id [{$entry['subject_legacy_id']}] is missing.");
            }

            if (! $subjectCategoryIds->contains($entry['subject_category_legacy_id'])) {
                throw new RuntimeException("Unable to seed curriculum entry [{$entry['legacy_id']}] because subject category id [{$entry['subject_category_legacy_id']}] is missing.");
            }

            CurriculumEntry::query()->updateOrCreate(
                [
                    'id' => $entry['legacy_id'],
                ],
                [
                    'curriculum_id' => $entry['curriculum_legacy_id'],
                    'subject_id' => $entry['subject_legacy_id'],
                    'subject_category_id' => $entry['subject_category_legacy_id'],
                    'semester' => $entry['semester'],
                    'year_level' => $entry['year_level'],
                    'created_at' => $entry['created_at'] ?? $timestamp,
                    'updated_at' => $entry['updated_at'] ?? $timestamp,
                ]
            );
        });
    }
}
