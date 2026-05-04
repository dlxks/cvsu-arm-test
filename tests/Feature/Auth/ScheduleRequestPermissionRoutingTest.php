<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

describe('schedule request permissions', function () {
    beforeEach(function () {
        collect(['collegeAdmin', 'deptAdmin'])->each(fn (string $role) => Role::findOrCreate($role, 'web'));

        collect([
            'schedule_requests.view',
            'schedule_requests.create',
            'schedule_requests.update',
            'schedule_requests.delete',
            'schedule_requests.approve',
            'schedules.view',
        ])->each(fn (string $permission) => Permission::findOrCreate($permission, 'web'));
    });

    it('requires the schedule request view permission for the college dashboard route', function () {
        $authorized = actingUserWithPermissions(['schedule_requests.view'], ['collegeAdmin']);

        $legacyOnly = User::factory()->create();
        /** @var User $legacyOnly */
        $legacyOnly->assignRole('collegeAdmin');
        $legacyOnly->givePermissionTo('schedules.view');

        $this->actingAs($authorized)
            ->get(route('schedule-service-requests.index'))
            ->assertOk();

        $this->actingAs($legacyOnly)
            ->get(route('schedule-service-requests.index'))
            ->assertForbidden();
    });

    it('requires the schedule request view permission for the department assignment route', function () {
        $authorized = actingUserWithPermissions(['schedule_requests.view'], ['deptAdmin']);

        $legacyOnly = User::factory()->create();
        /** @var User $legacyOnly */
        $legacyOnly->assignRole('deptAdmin');
        $legacyOnly->givePermissionTo('schedules.view');

        $this->actingAs($authorized)
            ->get(route('schedules.service-requests'))
            ->assertOk();

        $this->actingAs($legacyOnly)
            ->get(route('schedules.service-requests'))
            ->assertForbidden();
    });

    it('blocks college request creation without the dedicated create permission', function () {
        $user = actingUserWithPermissions(['schedule_requests.view'], ['collegeAdmin']);

        Livewire::actingAs($user)
            ->test('pages::college-admin.schedule-service-requests.index')
            ->call('createRequest')
            ->assertForbidden();
    });

    it('blocks department request updates without the dedicated update permission', function () {
        $user = actingUserWithPermissions(['schedule_requests.view'], ['deptAdmin']);

        Livewire::actingAs($user)
            ->test('pages::dept-admin.schedules.service-requests')
            ->call('submit', 1)
            ->assertForbidden();
    });
});