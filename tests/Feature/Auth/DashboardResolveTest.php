<?php

use App\Models\Permission;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('dashboard resolver', function () {
    beforeEach(function () {
        collect(['superAdmin', 'collegeAdmin', 'deptAdmin', 'faculty'])
            ->each(fn (string $role) => Role::findOrCreate($role, 'web'));

        collect([
            'campuses.view',
            'departments.view',
            'schedules.assign',
            'faculty_schedules.view',
        ])->each(fn (string $permission) => Permission::findOrCreate($permission, 'web'));
    });

    it('guest users are redirected to login when opening dashboard resolver', function () {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    });

        it('renders the dashboard for multi role users with valid permissions', function () {
        $user = User::factory()->create();
        /** @var User $user */
        $user->assignRole(['faculty', 'deptAdmin']);
        $user->givePermissionTo(['faculty_schedules.view', 'schedules.assign']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    });

    it('renders dashboard content for dept admins with permission only', function () {
        $user = User::factory()->create();
        /** @var User $user */
        $user->assignRole(['faculty', 'deptAdmin']);
        $user->givePermissionTo(['faculty_schedules.view', 'schedules.assign']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Schedule Status');
    });

    it('renders dashboard content for college users with permission only', function () {
        $user = User::factory()->create();
        /** @var User $user */
        $user->assignRole('faculty');
        $user->givePermissionTo('departments.view');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('permission(s) available to this account');
    });

    it('renders fallback dashboard message for users without permissions', function () {
        $user = User::factory()->create();
        /** @var User $user */

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No dashboard widgets are currently available for your account.');
    });
});
