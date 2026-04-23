<?php

use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Subject;
use App\Support\LegacyTimestampBackfill;
use Illuminate\Support\Facades\DB;

it('backfills legacy UTC timestamps to Philippine time and preserves date-only fields', function () {
    $campus = Campus::factory()->create();
    $college = College::factory()->forCampus($campus)->create();
    $department = Department::factory()->forCollege($college)->create();
    $program = Program::factory()->create();
    $subject = Subject::factory()->create();
    $college->programs()->attach($program->id);
    $subject->programs()->attach($program->id);

    $facultyProfile = FacultyProfile::factory()->forDepartment($department)->create([
        'birthday' => '1990-01-15',
    ]);

    $role = Role::findOrCreate('timezone-audit-role', 'web');
    $role->delete();

    DB::table('subjects')->where('id', $subject->id)->update([
        'created_at' => '2026-04-23 02:51:16',
        'updated_at' => '2026-04-23 03:05:16',
    ]);

    DB::table('subject_program')
        ->where('subject_id', $subject->id)
        ->where('program_id', $program->id)
        ->update([
            'created_at' => '2026-04-23 02:51:16',
            'updated_at' => '2026-04-23 03:05:16',
        ]);

    DB::table('faculty_profiles')->where('id', $facultyProfile->id)->update([
        'birthday' => '1990-01-15',
        'created_at' => '2026-04-23 02:51:16',
        'updated_at' => '2026-04-23 03:05:16',
    ]);

    DB::table('roles')->where('id', $role->id)->update([
        'created_at' => '2026-04-23 02:51:16',
        'updated_at' => '2026-04-23 03:05:16',
        'deleted_at' => '2026-04-23 04:10:16',
    ]);

    LegacyTimestampBackfill::apply(DB::connection());

    expect(DB::table('subjects')->where('id', $subject->id)->first(['created_at', 'updated_at']))
        ->toMatchObject((object) [
            'created_at' => '2026-04-23 10:51:16',
            'updated_at' => '2026-04-23 11:05:16',
        ])
        ->and(
            DB::table('subject_program')
                ->where('subject_id', $subject->id)
                ->where('program_id', $program->id)
                ->first(['created_at', 'updated_at'])
        )->toMatchObject((object) [
            'created_at' => '2026-04-23 10:51:16',
            'updated_at' => '2026-04-23 11:05:16',
        ])
        ->and(DB::table('faculty_profiles')->where('id', $facultyProfile->id)->first(['birthday', 'created_at', 'updated_at']))
        ->toMatchObject((object) [
            'birthday' => '1990-01-15',
            'created_at' => '2026-04-23 10:51:16',
            'updated_at' => '2026-04-23 11:05:16',
        ])
        ->and(DB::table('roles')->where('id', $role->id)->first(['created_at', 'updated_at', 'deleted_at']))
        ->toMatchObject((object) [
            'created_at' => '2026-04-23 10:51:16',
            'updated_at' => '2026-04-23 11:05:16',
            'deleted_at' => '2026-04-23 12:10:16',
        ]);
});
