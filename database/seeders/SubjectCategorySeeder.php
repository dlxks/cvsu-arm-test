<?php

namespace Database\Seeders;

use App\Models\SubjectCategory;
use Database\Seeders\Support\CvsuSeedData;
use Illuminate\Database\Seeder;

class SubjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CvsuSeedData::subjectCategories()->each(function (array $subjectCategory): void {
            SubjectCategory::query()->updateOrCreate(
                [
                    'id' => $subjectCategory['legacy_id'],
                ],
                [
                    'name' => $subjectCategory['name'],
                    'created_at' => $subjectCategory['created_at'],
                    'updated_at' => $subjectCategory['updated_at'],
                ]
            );
        });
    }
}
