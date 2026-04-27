<?php

use App\Imports\RoomsImport;
use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('RoomsImport', function () {
    it('normalizes display labels into database-safe room type and status values', function () {
        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        $rows = new Collection([
            collect([
                'name' => 'CCL',
                'floor_no' => '1',
                'room_no' => 101,
                'type' => 'Laboratory',
                'status' => 'Under Renovation',
                'location' => 'Main Building',
                'is_active' => 'yes',
            ]),
        ]);

        (new RoomsImport($department))->collection($rows);

        $room = Room::query()
            ->where('department_id', $department->id)
            ->where('room_no', 101)
            ->first();

        expect($room)->not->toBeNull()
            ->and($room->type)->toBe('LABORATORY')
            ->and($room->status)->toBe('UNDER_RENOVATION')
            ->and($room->type_label)->toBe('Laboratory')
            ->and($room->status_label)->toBe('Under Renovation');
    });
});
