<?php

use App\Models\Campus;
use App\Models\College;
use App\Models\Curriculum;
use App\Models\CurriculumEntry;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Program;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Schedule;
use App\Models\ScheduleCategory;
use App\Models\ScheduleSection;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\User;
use Livewire\Livewire;

describe('department admin schedule forms', function () {
    it('validates the bulk generation form through Livewire form fields', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.bulk-generate')
            ->call('generate')
            ->assertHasErrors([
                'form.program_id' => 'required',
                'form.year_level' => 'required',
            ]);
    });

    it('derives distinct year level options from the selected program curricula and clears stale selections', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        $subjectCategory = SubjectCategory::factory()->create();

        $programA = Program::factory()->create(['code' => 'BSIT']);
        $programA->colleges()->attach($college->id);

        $curriculumA1 = Curriculum::factory()->create([
            'program_id' => $programA->id,
            'year_implemented' => 2024,
        ]);
        $curriculumA2 = Curriculum::factory()->create([
            'program_id' => $programA->id,
            'year_implemented' => 2026,
        ]);

        $subjectA = Subject::factory()->create(['code' => 'IT111']);
        $subjectB = Subject::factory()->create(['code' => 'IT121']);
        $subjectC = Subject::factory()->create(['code' => 'IT211']);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculumA1->id,
            'subject_id' => $subjectA->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 1,
            'semester' => '1ST',
        ]);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculumA2->id,
            'subject_id' => $subjectB->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 1,
            'semester' => '2ND',
        ]);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculumA2->id,
            'subject_id' => $subjectC->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 2,
            'semester' => '1ST',
        ]);

        $programB = Program::factory()->create(['code' => 'BSCS']);
        $programB->colleges()->attach($college->id);

        $curriculumB = Curriculum::factory()->create([
            'program_id' => $programB->id,
            'year_implemented' => 2026,
        ]);

        $subjectD = Subject::factory()->create(['code' => 'CS311']);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculumB->id,
            'subject_id' => $subjectD->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 3,
            'semester' => '1ST',
        ]);

        $component = Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.bulk-generate');

        $component->set('form.program_id', $programA->id);

        expect($component->instance()->yearLevelOptions())->toBe([
            ['label' => 'Year 1', 'value' => 1],
            ['label' => 'Year 2', 'value' => 2],
        ]);

        $component
            ->set('form.year_level', 2)
            ->set('form.program_id', $programB->id)
            ->assertSet('form.year_level', null);

        expect($component->instance()->yearLevelOptions())->toBe([
            ['label' => 'Year 3', 'value' => 3],
        ]);
    });

    it('validates the custom section form through Livewire form fields', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.custom-section')
            ->call('createCustom')
            ->assertHasErrors([
                'form.subject_id' => 'required',
                'form.program_code' => 'required',
                'form.section_identifier' => 'required',
            ]);
    });

    it('validates the plotting form through Livewire form fields', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.plot')
            ->call('plot')
            ->assertHasErrors([
                'form.schedule_id' => 'required',
                'form.schedule_category_id' => 'required',
            ]);
    });

    it('successfully submits bulk generation and creates draft schedules', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        $program = Program::factory()->create([
            'code' => 'BSIT',
        ]);
        $program->colleges()->attach($college->id);

        $curriculum = Curriculum::factory()->create([
            'program_id' => $program->id,
            'year_implemented' => 2026,
        ]);

        $subjectCategory = SubjectCategory::factory()->create();

        $subjectA = Subject::factory()->create(['code' => 'IT101']);
        $subjectB = Subject::factory()->create(['code' => 'IT102']);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculum->id,
            'subject_id' => $subjectA->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 1,
            'semester' => '1ST',
        ]);

        CurriculumEntry::factory()->create([
            'curriculum_id' => $curriculum->id,
            'subject_id' => $subjectB->id,
            'subject_category_id' => $subjectCategory->id,
            'year_level' => 1,
            'semester' => '1ST',
        ]);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.bulk-generate')
            ->set('form.program_id', $program->id)
            ->set('form.year_level', 1)
            ->set('form.semester', '1ST')
            ->set('form.school_year', '2026-2027')
            ->set('form.section_count', 2)
            ->set('form.slots', 45)
            ->call('generate')
            ->assertHasNoErrors();

        $generatedSchedules = Schedule::query()
            ->where('campus_id', $campus->id)
            ->where('college_id', $college->id)
            ->where('department_id', $department->id)
            ->where('semester', '1ST')
            ->where('school_year', '2026-2027')
            ->where('status', 'draft')
            ->get();

        expect($generatedSchedules)->toHaveCount(4)
            ->and($generatedSchedules->pluck('slots')->unique()->values()->all())->toBe([45]);

        $scheduleIds = $generatedSchedules->pluck('id')->all();

        expect(ScheduleSection::query()->whereIn('schedule_id', $scheduleIds)->count())->toBe(4)
            ->and(
                ScheduleSection::query()
                    ->whereIn('schedule_id', $scheduleIds)
                    ->whereIn('computed_section_name', ['BSIT1-1', 'BSIT1-2'])
                    ->count()
            )->toBe(4);
    });

    it('successfully submits custom section creation and persists section metadata', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        $subject = Subject::factory()->create([
            'code' => 'CS201',
            'title' => 'Data Structures',
        ]);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.custom-section')
            ->set('form.subject_id', $subject->id)
            ->set('form.program_code', 'bsit')
            ->set('form.year_level', 3)
            ->set('form.section_identifier', 'irc1')
            ->set('form.section_type', 'PETITION')
            ->set('form.semester', '2ND')
            ->set('form.school_year', '2026-2027')
            ->set('form.slots', 35)
            ->call('createCustom')
            ->assertHasNoErrors();

        $createdSchedule = Schedule::query()
            ->where('campus_id', $campus->id)
            ->where('college_id', $college->id)
            ->where('department_id', $department->id)
            ->where('subject_id', $subject->id)
            ->where('semester', '2ND')
            ->where('school_year', '2026-2027')
            ->latest('id')
            ->first();

        expect($createdSchedule)->not->toBeNull()
            ->and($createdSchedule?->status)->toBe('draft')
            ->and($createdSchedule?->slots)->toBe(35);

        $createdSection = ScheduleSection::query()->where('schedule_id', $createdSchedule->id)->first();

        expect($createdSection)->not->toBeNull()
            ->and($createdSection?->program_code)->toBe('BSIT')
            ->and($createdSection?->section_identifier)->toBe('IRC1')
            ->and($createdSection?->section_type)->toBe('PETITION')
            ->and($createdSection?->computed_section_name)->toBe('BSIT3-IRC1');
    });

    it('successfully submits plotting and stores room/faculty assignments', function () {
        $user = actingUserWithPermissions([
            'schedules.create',
            'schedules.assign',
        ], ['deptAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        $subject = Subject::factory()->create();

        $schedule = Schedule::query()->create([
            'sched_code' => '260100001',
            'subject_id' => $subject->id,
            'campus_id' => $campus->id,
            'college_id' => $college->id,
            'department_id' => $department->id,
            'semester' => '1ST',
            'school_year' => '2026-2027',
            'slots' => 40,
            'status' => 'draft',
        ]);

        ScheduleSection::query()->create([
            'schedule_id' => $schedule->id,
            'program_code' => 'BSIT',
            'year_level' => 1,
            'section_identifier' => '1',
            'section_type' => 'REGULAR',
            'computed_section_name' => 'BSIT1-1',
        ]);

        $scheduleCategory = ScheduleCategory::factory()->create([
            'name' => 'LECTURE E2E',
            'slug' => 'lecture-e2e',
            'is_active' => true,
        ]);

        $roomCategory = RoomCategory::factory()->create();
        $room = Room::factory()->create([
            'campus_id' => $campus->id,
            'college_id' => $college->id,
            'department_id' => $department->id,
            'room_category_id' => $roomCategory->id,
        ]);

        $faculty = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.plot')
            ->set('form.schedule_id', $schedule->id)
            ->set('form.schedule_category_id', $scheduleCategory->id)
            ->set('form.day', 'MON')
            ->set('form.time_in', '08:00')
            ->set('form.time_out', '10:00')
            ->set('form.faculty_id', $faculty->id)
            ->set('form.room_id', $room->id)
            ->call('plot')
            ->assertHasNoErrors();

        expect($schedule->fresh()->status)->toBe('plotted');

        $roomTime = $schedule->roomTimes()
            ->where('schedule_category_id', $scheduleCategory->id)
            ->where('day', 'MON')
            ->first();

        expect($roomTime)->not->toBeNull()
            ->and($roomTime?->room_id)->toBe($room->id)
            ->and(substr((string) $roomTime?->time_in, 0, 5))->toBe('08:00')
            ->and(substr((string) $roomTime?->time_out, 0, 5))->toBe('10:00');

        $facultyAssignment = $schedule->facultyAssignments()
            ->where('schedule_category_id', $scheduleCategory->id)
            ->first();

        expect($facultyAssignment)->not->toBeNull()
            ->and($facultyAssignment?->user_id)->toBe($faculty->id);
    });
});
