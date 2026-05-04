<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ScheduleCategory;
use Database\Factories\ScheduleCategoryFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ScheduleCategorySeeder extends Seeder
{
    public function run(): void
    {
        collect(ScheduleCategoryFactory::DEFAULT_CATEGORY_NAMES)->each(function (string $name): void {
            $category = ScheduleCategory::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ScheduleCategory::factory()->named($name)->make()->only(['name', 'slug', 'is_active'])
            );

            if ($category->trashed()) {
                $category->restore();
            }
        });
    }
}