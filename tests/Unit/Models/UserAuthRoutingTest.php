<?php

use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    collect(['superAdmin', 'collegeAdmin', 'deptAdmin', 'faculty'])
        ->each(fn (string $role) => Role::findOrCreate($role, 'web'));
});

test('canUseGoogleSignIn returns false when user has no supported role', function () {
    $user = User::factory()->create();

    expect($user->canUseGoogleSignIn())->toBeFalse();
});

test('canUseGoogleSignIn requires matching profile for faculty role', function () {
    $user = User::factory()->create();
    $user->assignRole('faculty');

    expect($user->canUseGoogleSignIn())->toBeFalse();

    FacultyProfile::factory()->create([
        'user_id' => $user->id,
        'email' => $user->email,
    ]);

    expect($user->fresh()->canUseGoogleSignIn())->toBeTrue();
});

test('dashboardRoute follows superAdmin collegeAdmin deptAdmin faculty priority', function () {
    $user = User::factory()->create();
    $user->assignRole(['faculty', 'deptAdmin']);

    FacultyProfile::factory()->create([
        'user_id' => $user->id,
        'email' => $user->email,
    ]);

    EmployeeProfile::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->fresh()->dashboardRoute())->toBe('department-admin.dashboard');

    $user->assignRole('collegeAdmin');

    expect($user->fresh()->dashboardRoute())->toBe('college-admin.dashboard');

    $user->assignRole('superAdmin');

    expect($user->fresh()->dashboardRoute())->toBe('admin.dashboard');
});

test('syncGoogleProfile persists provider identity and avatar', function () {
    $user = User::factory()->create();

    $user->syncGoogleProfile('google-456', 'https://example.test/me.png');

    $fresh = $user->fresh();

    expect($fresh->google_id)->toBe('google-456')
        ->and($fresh->avatar)->toBe('https://example.test/me.png')
        ->and($fresh->email_verified_at)->not->toBeNull();
});
