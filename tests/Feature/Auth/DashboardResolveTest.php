<?php

use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    collect(['superAdmin', 'collegeAdmin', 'deptAdmin', 'faculty'])
        ->each(fn (string $role) => Role::findOrCreate($role, 'web'));
});

test('guest users are redirected to login when opening dashboard resolver', function () {
    $this->get(route('dashboard.resolve'))
        ->assertRedirect(route('login'));
});

test('dashboard resolver uses configured role priority for multi role users', function () {
    $user = User::factory()->create();
    $user->assignRole(['faculty', 'deptAdmin']);

    FacultyProfile::factory()->create([
        'user_id' => $user->id,
        'email' => $user->email,
    ]);

    EmployeeProfile::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.resolve'))
        ->assertRedirect(route('department-admin.dashboard'));
});

test('dashboard resolver returns forbidden for users without valid dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.resolve'))
        ->assertForbidden();
});
