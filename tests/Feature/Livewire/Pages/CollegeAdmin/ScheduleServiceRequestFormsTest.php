<?php

use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Schedule;
use App\Models\ScheduleServiceRequest;
use App\Models\Subject;
use Livewire\Livewire;

describe('college admin schedule service request forms', function () {
    it('validates create request fields via Livewire form object', function () {
        $user = actingUserWithPermissions([
            'schedule_requests.view',
            'schedule_requests.create',
            'schedule_requests.approve',
        ], ['collegeAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test('pages::college-admin.schedule-service-requests.index')
            ->call('createRequest')
            ->assertHasErrors([
                'requestForm.section_names' => 'required',
                'requestForm.schedule_ids' => 'required',
                'requestForm.servicing_college_id' => 'required',
            ]);
    });

    it('validates assign-to-department fields via Livewire form object', function () {
        $user = actingUserWithPermissions([
            'schedule_requests.view',
            'schedule_requests.create',
            'schedule_requests.approve',
        ], ['collegeAdmin']);

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $department = Department::factory()->forCollege($college)->create();

        EmployeeProfile::factory()->forDepartment($department)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test('pages::college-admin.schedule-service-requests.index')
            ->call('assignToDepartment')
            ->assertHasErrors([
                'assignForm.service_request_id' => 'required',
                'assignForm.department_id' => 'required',
            ]);
    });

    it('successfully delegates a service request to a department', function () {
        $user = actingUserWithPermissions([
            'schedule_requests.view',
            'schedule_requests.approve',
        ], ['collegeAdmin']);

        $campus = Campus::factory()->create();

        $servicingCollege = College::factory()->forCampus($campus)->create();
        $servicingDepartment = Department::factory()->forCollege($servicingCollege)->create();

        EmployeeProfile::factory()->forDepartment($servicingDepartment)->create([
            'user_id' => $user->id,
        ]);

        $requestingCollege = College::factory()->forCampus($campus)->create();
        $requestingDepartment = Department::factory()->forCollege($requestingCollege)->create();

        $subject = Subject::factory()->create();

        $schedule = Schedule::query()->create([
            'sched_code' => '260100099',
            'subject_id' => $subject->id,
            'campus_id' => $campus->id,
            'college_id' => $requestingCollege->id,
            'department_id' => $requestingDepartment->id,
            'semester' => '1ST',
            'school_year' => '2026-2027',
            'slots' => 40,
            'status' => 'pending_plotting',
        ]);

        $serviceRequest = ScheduleServiceRequest::query()->create([
            'requesting_college_id' => $requestingCollege->id,
            'servicing_college_id' => $servicingCollege->id,
            'status' => 'accepted',
            'assigned_department_id' => null,
        ]);

        $serviceRequest->schedules()->attach($schedule->id);

        Livewire::actingAs($user)
            ->test('pages::college-admin.schedule-service-requests.index')
            ->set('assignForm.service_request_id', $serviceRequest->id)
            ->set('assignForm.department_id', $servicingDepartment->id)
            ->call('assignToDepartment')
            ->assertHasNoErrors();

        expect($serviceRequest->fresh()->status)->toBe('assigned_to_dept')
            ->and($serviceRequest->fresh()->assigned_department_id)->toBe($servicingDepartment->id)
            ->and($schedule->fresh()->department_id)->toBe($servicingDepartment->id)
            ->and($schedule->fresh()->status)->toBe('pending_plotting');
    });
});
