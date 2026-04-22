<?php

namespace Database\Seeders;

use App\Models\CurriculumEntry;
use App\Models\Prerequisite;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;
use RuntimeException;

class PrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curriculumEntryIds = CurriculumEntry::query()->pluck('id');
        $timestamp = now();

        CvsuSeedData::prerequisites()->each(function (array $prerequisite) use ($curriculumEntryIds, $timestamp): void {
            if (! $curriculumEntryIds->contains($prerequisite['curriculum_entry_legacy_id'])) {
                throw new RuntimeException("Unable to seed prerequisite [{$prerequisite['legacy_id']}] because curriculum entry id [{$prerequisite['curriculum_entry_legacy_id']}] is missing.");
            }

            Prerequisite::query()->updateOrCreate(
                [
                    'id' => $prerequisite['legacy_id'],
                ],
                [
                    'curriculum_entry_id' => $prerequisite['curriculum_entry_legacy_id'],
                    'label' => $prerequisite['label'],
                    'created_at' => $prerequisite['created_at'] ?? $timestamp,
                    'updated_at' => $prerequisite['updated_at'] ?? $timestamp,
                ]
            );
        });
    }
}
