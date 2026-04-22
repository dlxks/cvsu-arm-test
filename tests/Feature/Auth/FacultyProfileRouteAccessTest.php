<?php

use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\Permission;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('faculty profile route scope access', function () {
    beforeEach(function () {
        collect(['collegeAdmin', 'deptAdmin'])
            ->each(fn (string $role) => Role::findOrCreate($role, 'web'));

        Permission::findOrCreate('faculty_profiles.view', 'web');

        $this->campus = Campus::factory()->create();
        $this->college = College::factory()->forCampus($this->campus)->create();
        $this->department = Department::factory()->forCollege($this->college)->create();
    });

    it('allows college faculty route and forbids department faculty route for college-assigned users without department', function () {
        $user = User::factory()->collegeAdmin()->create();
        $user->givePermissionTo('faculty_profiles.view');
        $user->employeeProfile->update([
            'campus_id' => $this->campus->id,
            'college_id' => $this->college->id,
            'department_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('college-faculty-profiles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty-profiles.index'))
            ->assertForbidden();
    });

    it('allows both faculty routes for users with both collegeAdmin and deptAdmin roles', function () {
        $user = User::factory()->create();
        $user->syncRoles(['collegeAdmin', 'deptAdmin']);
        $user->givePermissionTo('faculty_profiles.view');
        $user->employeeProfile()->updateOrCreate(['user_id' => $user->id], [
            'user_id' => $user->id,
            'employee_no' => 'EMP-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Dual',
            'last_name' => 'Admin',
            'position' => 'Administrator',
            'campus_id' => $this->campus->id,
            'college_id' => $this->college->id,
            'department_id' => $this->department->id,
        ]);

        $this->actingAs($user)
            ->get(route('college-faculty-profiles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty-profiles.index'))
            ->assertOk();
    });

    it('forbids college faculty route and allows department faculty route for deptAdmin-only users', function () {
        $user = User::factory()->deptAdmin()->create();
        $user->givePermissionTo('faculty_profiles.view');
        $user->employeeProfile->update([
            'campus_id' => $this->campus->id,
            'college_id' => $this->college->id,
            'department_id' => $this->department->id,
        ]);

        $this->actingAs($user)
            ->get(route('college-faculty-profiles.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('faculty-profiles.index'))
            ->assertOk();
    });

    it('allows college route by direct permission and assignment while denying department route without department', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('faculty_profiles.view');
        $user->employeeProfile()->updateOrCreate(['user_id' => $user->id], [
            'user_id' => $user->id,
            'employee_no' => 'EMP-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Direct',
            'last_name' => 'Permission',
            'position' => 'Staff',
            'campus_id' => $this->campus->id,
            'college_id' => $this->college->id,
            'department_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('college-faculty-profiles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty-profiles.index'))
            ->assertForbidden();
    });
});