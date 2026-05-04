<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

describe('schedule categories migration schema', function () {
    it('creates scheduling tables with schedule category foreign keys in a fresh migration', function () {
        Schema::dropAllTables();

        foreach ([
            '2026_04_08_032522_create_campuses_table.php',
            '2026_04_08_033311_create_colleges_table.php',
            '2026_04_08_033637_create_departments_table.php',
            '2026_04_24_030654_create_subjects_table.php',
            '2026_04_30_000000_create_schedule_categories_table.php',
            '2026_04_30_000002_create_scheduling_tables.php',
        ] as $migrationFile) {
            (require database_path('migrations/'.$migrationFile))->up();
        }

        expect(Schema::hasColumn('schedule_room_time', 'schedule_category_id'))->toBeTrue()
            ->and(Schema::hasColumn('schedule_room_time', 'class_type'))->toBeFalse()
            ->and(Schema::hasColumn('schedule_faculty', 'schedule_category_id'))->toBeTrue()
            ->and(Schema::hasColumn('schedule_faculty', 'class_type'))->toBeFalse();

        DB::table('campuses')->insert([
            'id' => 1,
            'name' => 'Main Campus',
            'code' => 'MC',
            'description' => 'Test campus',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colleges')->insert([
            'id' => 1,
            'campus_id' => 1,
            'name' => 'College of Engineering',
            'code' => 'COE',
            'description' => 'Test college',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subjects')->insert([
            'id' => 1,
            'code' => 'TEST100',
            'title' => 'Test Subject',
            'description' => 'Test description',
            'lecture_units' => 3,
            'laboratory_units' => 0,
            'is_credit' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('schedule_categories')->insert([
            'id' => 1,
            'name' => 'LECTURE',
            'slug' => 'lecture',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'id' => 1,
            'sched_code' => '260100123',
            'subject_id' => 1,
            'campus_id' => 1,
            'college_id' => 1,
            'department_id' => null,
            'semester' => '1ST',
            'school_year' => '2026-2027',
            'slots' => 40,
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('schedule_faculty')->insert([
            'schedule_id' => 1,
            'user_id' => null,
            'schedule_category_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        expect(DB::table('schedule_faculty')->where('schedule_id', 1)->value('schedule_category_id'))->toBe(1);
    });
});
