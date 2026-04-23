<?php

use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

describe('department admin faculty profiles page', function () {
    beforeEach(function () {
        ensureRoles(['faculty', 'deptAdmin']);

        $this->user = actingUserWithPermissions([
            'faculty_profiles.view',
            'faculty_profiles.create',
        ], ['deptAdmin']);

        $this->campus = Campus::factory()->create();
        $this->college = College::factory()->forCampus($this->campus)->create();
        $this->department = Department::factory()->forCollege($this->college)->create();

        EmployeeProfile::factory()->forDepartment($this->department)->create([
            'user_id' => $this->user->id,
        ]);
    });

    it('initializes scoped assignment defaults for dept admins', function () {
        Livewire::actingAs($this->user)
            ->test('pages::dept-admin.faculty-profiles.index')
            ->call('create')
            ->assertSet('form.campus_id', $this->campus->id)
            ->assertSet('form.college_id', $this->college->id)
            ->assertSet('form.department_id', $this->department->id)
            ->assertCount('departments', 1);
    });

    it('creates a faculty profile and linked faculty user', function () {
        Livewire::actingAs($this->user)
            ->test('pages::dept-admin.faculty-profiles.index')
            ->call('create')
            ->set('form.first_name', 'Mara')
            ->set('form.middle_name', 'N')
            ->set('form.last_name', 'Reyes')
            ->set('form.email', 'mara.reyes@example.test')
            ->set('form.academic_rank', 'Assistant Professor I')
            ->set('form.contactno', '09181234567')
            ->set('form.sex', 'Female')
            ->set('form.address', 'Naic, Cavite')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('createModal', false)
            ->assertDispatched('pg:eventRefresh-facultyProfilesTable');

        $createdUser = User::query()->where('email', 'mara.reyes@example.test')->first();

        expect($createdUser)->not->toBeNull()
            ->and($createdUser->hasRole('faculty'))->toBeTrue()
            ->and($createdUser->facultyProfile)->not->toBeNull()
            ->and($createdUser->facultyProfile->department_id)->toBe($this->department->id)
            ->and($createdUser->facultyProfile->academic_rank)->toBe('Assistant Professor I');
    });

    it('forbids dept admins without a department assignment from accessing the page', function () {
        $facultyOnlyAdmin = actingUserWithPermissions([
            'faculty_profiles.view',
            'faculty_profiles.create',
        ], ['deptAdmin']);

        $this->actingAs($facultyOnlyAdmin)
            ->get(route('faculty-profiles.index'))
            ->assertForbidden();
    });

    it('renders faculty profile timestamps in Philippine time on the detail page', function () {
        $profile = FacultyProfile::factory()->forDepartment($this->department)->create();

        DB::table('faculty_profiles')->where('id', $profile->id)->update([
            'created_at' => '2026-04-23 10:51:41',
            'updated_at' => '2026-04-23 11:31:21',
        ]);

        Livewire::actingAs($this->user)
            ->test('pages::dept-admin.faculty-profiles.show', ['facultyProfile' => $profile->fresh()])
            ->assertSee('Apr 23, 2026 10:51 AM')
            ->assertSee('Apr 23, 2026 11:31 AM');
    });
});
