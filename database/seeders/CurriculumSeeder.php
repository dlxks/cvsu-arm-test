<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\Program;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;
use RuntimeException;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programIds = Program::query()->pluck('id');
        $timestamp = now();

        CvsuSeedData::curricula()->each(function (array $curriculum) use ($programIds, $timestamp): void {
            if (! $programIds->contains($curriculum['program_legacy_id'])) {
                throw new RuntimeException("Unable to seed curriculum [{$curriculum['legacy_id']}] because program id [{$curriculum['program_legacy_id']}] is missing.");
            }

            Curriculum::query()->updateOrCreate(
                [
                    'id' => $curriculum['legacy_id'],
                ],
                [
                    'program_id' => $curriculum['program_legacy_id'],
                    'title' => $curriculum['title'],
                    'year_implemented' => $curriculum['year_implemented'],
                    'created_at' => $curriculum['created_at'] ?? $timestamp,
                    'updated_at' => $curriculum['updated_at'] ?? $timestamp,
                ]
            );
        });
    }
}
