<?php

use App\Models\CurriculumEntry;
use App\Models\Permission;
use App\Models\Program;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Program model', function () {
    beforeEach(function () {
        $this->program = Program::factory()->make();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->program->getFillable())->toBe([
            'code',
            'title',
            'description',
            'no_of_years',
            'level',
            'is_active',
        ]);
    });

    it('exposes human readable level and duration labels', function () {
        $program = Program::factory()->make([
            'level' => 'UNDERGRADUATE',
            'no_of_years' => 4,
        ]);

        expect($program->level_label)->toBe('Undergraduate')
            ->and($program->duration_label)->toBe('4 years');
    });

    it('builds normalized level select options from raw stored values', function () {
        expect(Program::levelOptions(['GRADUATE', 'UNDERGRADUATE', 'GRADUATE']))->toBe([
            ['id' => 'GRADUATE', 'name' => 'Graduate'],
            ['id' => 'UNDERGRADUATE', 'name' => 'Undergraduate'],
        ]);
    });
});

describe('CurriculumEntry model', function () {
    it('builds semester labels and option arrays from curriculum values', function () {
        expect(CurriculumEntry::semesterLabel('1ST'))->toBe('1st Semester')
            ->and(CurriculumEntry::semesterFilterOptions(['2ND', 'SUMMER']))->toBe([
                ['id' => '2ND', 'name' => '2nd Semester'],
                ['id' => 'SUMMER', 'name' => 'Summer'],
            ])
            ->and(CurriculumEntry::semesterSelectOptions(['2ND', 'SUMMER']))->toBe([
                ['label' => '2nd Semester', 'value' => '2ND'],
                ['label' => 'Summer', 'value' => 'SUMMER'],
            ]);
    });
});

describe('Schedule model', function () {
    it('builds human readable status labels and filter options', function () {
        expect(Schedule::statusLabel('pending_plotting'))->toBe('Pending Plotting')
            ->and(Schedule::statusFilterOptions(['draft', 'published']))->toBe([
                ['id' => 'draft', 'name' => 'Draft'],
                ['id' => 'published', 'name' => 'Published'],
            ]);
    });
});

describe('Room model', function () {
    beforeEach(function () {
        $this->room = Room::factory()->make();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->room->getFillable())->toBe([
            'campus_id',
            'college_id',
            'department_id',
            'name',
            'floor_no',
            'room_no',
            'room_category_id',
            'description',
            'location',
            'is_active',
            'status',
        ]);
    });

    it('resolves category labels and sensible display names when the room number is optional', function () {
        $category = RoomCategory::query()->firstOrCreate(
            ['slug' => 'auditorium'],
            ['name' => 'Auditorium', 'is_active' => true]
        );

        $room = Room::factory()->make([
            'name' => 'Main Auditorium',
            'room_no' => null,
            'room_category_id' => $category->id,
        ]);
        $room->setRelation('roomCategory', $category);

        expect($room->type_label)->toBe('Auditorium')
            ->and($room->display_name)->toBe('Main Auditorium');
    });

    it('exposes reusable room status filter options', function () {
        expect(Room::statusFilterOptions(['useable', 'under_renovation']))->toBe([
            ['id' => 'useable', 'name' => 'Useable'],
            ['id' => 'under_renovation', 'name' => 'Under Renovation'],
        ]);
    });
});

describe('RoomCategory model', function () {
    beforeEach(function () {
        $this->roomCategory = new RoomCategory();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->roomCategory->getFillable())->toBe(['name', 'slug', 'is_active']);
    });
});

describe('Subject model', function () {
    beforeEach(function () {
        $this->subject = Subject::factory()->make();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->subject->getFillable())->toBe([
            'code',
            'title',
            'description',
            'lecture_units',
            'laboratory_units',
            'is_credit',
            'is_active',
        ]);
    });
});

describe('Role model', function () {
    beforeEach(function () {
        $this->role = new Role();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->role->getFillable())->toBe(['name', 'guard_name']);
    });
});

describe('Permission model', function () {
    beforeEach(function () {
        $this->permission = new Permission();
    });

    it('defines the expected fillable attributes', function () {
        expect($this->permission->getFillable())->toBe(['name', 'guard_name']);
    });
});
